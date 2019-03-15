<?php


namespace AppBundle\Service;

use AppBundle\Entity\SurveyNotification;
use AppBundle\Entity\SurveyNotificationCollection;
use AppBundle\Entity\SurveyTaken;
use AppBundle\Mailer\Mailer;
use AppBundle\Sms\Sms;
use AppBundle\Sms\SmsSenderInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\RouterInterface;

class SurveyNotifier
{
    private $em;
    private $mailer;
    private $twig;
    private $logger;
    private $router;
    private $smsSender;
    private $fromEmail;



    /**
     * SurveyNotifier constructor.
     * @param string $fromEmail
     * @param Mailer $mailer
     * @param \Twig_Environment $twig
     * @param LoggerInterface $logger
     * @param EntityManager $em
     * @param RouterInterface $router
     * @param SmsSenderInterface $smsSender
     */
    public function __construct(string $fromEmail, Mailer $mailer, \Twig_Environment $twig, LoggerInterface $logger, EntityManager $em, RouterInterface $router, SmsSenderInterface $smsSender)
    {
        $this->fromEmail = $fromEmail;
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->logger = $logger;
        $this->em = $em;
        $this->router = $router;
        $this->smsSender = $smsSender;
    }

    public function initializeSurveyNotifier(SurveyNotificationCollection $surveyNotificationCollection)
    {
        $userGroup = $surveyNotificationCollection->getUserGroup();
        $userGroup->setActive(true);
        $userGroupCollection = $userGroup ->getUserGroupCollection();
        $userGroupCollection->setDeletable(false);

        $this->em->persist($surveyNotificationCollection);
        $this->em->persist($userGroup);
        $this->em->flush();
    }

    private function createSurveyNotifications(SurveyNotificationCollection $surveyNotificationCollection)
    {
        if ($surveyNotificationCollection->isActive()) {
            return false;
        }

        $survey = $surveyNotificationCollection->getSurvey();
        $users = $surveyNotificationCollection->getUserGroup()->getUsers();
        foreach ($users as $user) {
            $isSurveyTakenByUser = !empty($this->em->getRepository(SurveyTaken::class)->findAllBySurveyAndUser($survey, $user));
            if ($isSurveyTakenByUser) {
                continue;
            }

            $notification = new SurveyNotification();
            $notification->setUser($user);
            $notification->setSurveyNotificationCollection($surveyNotificationCollection);

            $this->em->persist($notification);
        }
        $surveyNotificationCollection->getUserGroup()->setActive(true);
        $this->em->persist($surveyNotificationCollection->getUserGroup());

        try {
            $this->em->flush();
        } catch (UniqueConstraintViolationException $e) {
            return true;
        }
        return false;
    }


    public function sendNotifications(SurveyNotificationCollection $surveyNotificationCollection)
    {
        if (!$surveyNotificationCollection->isActive()) {
            $isIdentifierCollision = $this->createSurveyNotifications($surveyNotificationCollection);
            if ($isIdentifierCollision) {
                return true;
            }
        }

        $surveyNotificationCollection->setActive(true);
        $this->isAllSent($surveyNotificationCollection);
        $this->em->persist($surveyNotificationCollection);
        $this->em->flush();

        if ($surveyNotificationCollection->isAllSent()) {
            return false;
        } elseif ($surveyNotificationCollection->getNotificationType() === SurveyNotificationCollection::$SMS_NOTIFICATION) {
            $this->sendSMS($surveyNotificationCollection);
        } elseif ($surveyNotificationCollection->getNotificationType() === SurveyNotificationCollection::$EMAIL_NOTIFICATION) {
            $this->sendEmail($surveyNotificationCollection);
        }

        return false;
    }


    private function sendSMS(SurveyNotificationCollection $surveyNotificationCollection)
    {
        $numSmsSent = 0;
        $surveyid = $surveyNotificationCollection->getSurvey()->getId();
        $customMessage = $surveyNotificationCollection->getSmsMessage();
        foreach ($surveyNotificationCollection->getSurveyNotifications() as $notification) {
            if ($notification->isSent()) {
                return;
            }
            $notification->setSent(true);
            $notification->setTimeNotificationSent(new \DateTime());
            $this->em->persist($notification);

            $user = $notification->getUser();
            $identifier = $notification->getUserIdentifier();
            $phoneNumber = $user->getPhone();
            $validNumber = $this->smsSender->validatePhoneNumber($phoneNumber);
            if (!$validNumber) {
                $this->logger->alert("Kunne ikke sende schedule sms til *$user*\n Tlf.nr.: *$phoneNumber*");
                continue;
            }

            $message =
                "Hei, ".$notification->getUser()->getFirstName()."\n".
                $customMessage."\n".
                $this->router->generate(
                    'survey_show_user_id',
                    ['id' => $surveyid, 'userid'=>$identifier],
                    RouterInterface::ABSOLUTE_URL
                ) .
                "\n\n" .
                "Med vennlig hilsen\n" .
                "Vektorevaluering";

            $sms = new Sms();
            $sms->setMessage($message);
            $sms->setSender("Vektor");
            $sms->setRecipients([$phoneNumber]);
            $this->smsSender->send($sms);
            $numSmsSent += 1;
            $this->em->flush();

        }

        $this->logger->info("*$numSmsSent* notifications SMSs' sent about survey");
        $this->isAllSent($surveyNotificationCollection);
    }

    private function sendEmail(SurveyNotificationCollection $surveyNotificationCollection)
    {
        $numEmailSent = 0;
        $surveyId = $surveyNotificationCollection->getSurvey()->getId();
        $content = $surveyNotificationCollection->getEmailMessage();
        $subject = $surveyNotificationCollection->getEmailSubject();
        foreach ($surveyNotificationCollection->getSurveyNotifications() as $notification) {
            if ($notification->isSent()) {
                return;
            }
            $notification->setSent(true);
            $notification->setTimeNotificationSent(new \DateTime());
            $this->em->persist($notification);


            $user = $notification->getUser();
            $identifier = $notification->getUserIdentifier();
            $email = $user->getEmail();

            $message = (new \Swift_Message())
                ->setFrom(array($this->fromEmail => 'Vektorprogrammet'))
                ->setSubject($subject)
                ->setTo($email)
                ->setReplyTo($this->fromEmail)
                ->setBody(
                    $this->twig->render(
                        'survey/email_notification.html.twig',
                        array(
                            'firstname' => $notification->getUser()->getFirstName(),
                            'route' => $this->router->generate('survey_show_user_id', ['id' => $surveyId, 'userid'=>$identifier], RouterInterface::ABSOLUTE_URL),
                            'content' => $content,

                        )
                    ),
                    'text/html'
                );
            $this->mailer->send($message);
            $numEmailSent += 1;
            $this->em->flush();
        }
        $this->logger->info("*$numEmailSent* notifications emails sent about survey");
        $this->isAllSent($surveyNotificationCollection);
    }

    private function isAllSent(SurveyNotificationCollection $surveyNotificationCollection)
    {
        if ($surveyNotificationCollection->isAllSent()) {
            return true;
        }

        foreach ($surveyNotificationCollection->getSurveyNotifications() as $notification) {
            if (!$notification->isSent()) {
                return false;
            }
        }

        $surveyNotificationCollection->setAllSent(true);
        $this->em->persist($surveyNotificationCollection);
        $this->em->flush();
        return true;
    }
}
