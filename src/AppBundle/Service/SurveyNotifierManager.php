<?php


namespace AppBundle\Service;

use AppBundle\Entity\SurveyNotification;
use AppBundle\Entity\SurveyNotifier;
use AppBundle\Entity\SurveyTaken;
use AppBundle\Entity\User;
use AppBundle\Mailer\Mailer;
use AppBundle\Sms\Sms;
use AppBundle\Sms\SmsSenderInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\RouterInterface;

class SurveyNotifierManager
{
    private $em;
    private $mailer;
    private $twig;
    private $logger;
    private $router;
    private $smsSender;
    private $fromEmail;



    /**
     * SurveyNotifierManager constructor.
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

    public function initializeSurveyNotifier(SurveyNotifier $surveyNotifier)
    {
        $userGroup = $surveyNotifier->getUserGroup();
        $userGroup->setActive(true);
        $userGroupCollection = $userGroup ->getUserGroupCollection();
        $userGroupCollection->setDeletable(false);

        $this->em->persist($surveyNotifier);
        $this->em->persist($userGroup);
        $this->em->flush();
    }

    private function createSurveyNotifications(SurveyNotifier $surveyNotifier)
    {
        if ($surveyNotifier->isActive()) {
            return false;
        }

        $survey = $surveyNotifier->getSurvey();
        $users = $surveyNotifier->getUserGroup()->getUsers();
        foreach ($users as $user) {
            $isSurveyTakenByUser = !empty($this->em->getRepository(SurveyTaken::class)->findAllBySurveyAndUser($survey, $user));
            if ($isSurveyTakenByUser) {
                continue;
            }

            $notification = new SurveyNotification();
            $notification->setUser($user);
            $notification->setSurveyNotifier($surveyNotifier);

            $this->em->persist($notification);
        }
        $surveyNotifier->getUserGroup()->setActive(true);
        $this->em->persist($surveyNotifier->getUserGroup());

        try {
            $this->em->flush();
        } catch (UniqueConstraintViolationException $e) {
            return true;
        }
    }


    public function sendNotifications(SurveyNotifier $surveyNotifier)
    {
        $isIdentifierCollision = $this->createSurveyNotifications($surveyNotifier);
        if ($isIdentifierCollision) {
            return true;
        }

        $this->isAllSent($surveyNotifier);
        $surveyNotifier->setActive(true);
        $this->em->persist($surveyNotifier);
        $this->em->flush();

        if ($surveyNotifier->isAllSent()) {
            return;
        } elseif ($surveyNotifier->getNotificationType() === SurveyNotifier::$SMS_NOTIFICATION) {
            $this->sendSMS($surveyNotifier);
        } elseif ($surveyNotifier->getNotificationType() === SurveyNotifier::$EMAIL_NOTIFICATION) {
            $this->sendEmail($surveyNotifier);
        }

        return false;
    }


    private function sendSMS(SurveyNotifier $surveyNotifier)
    {
        $surveyid = $surveyNotifier->getSurvey()->getId();
        $customMessage = $surveyNotifier->getSmsMessage();
        foreach ($surveyNotifier->getSurveyNotifications() as $notification) {
            if ($notification->isSent()) {
                return;
            }
            $notification->setSent(true);
            $this->em->persist($notification);
            $notification->setTimeNotificationSent(new \DateTime());
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
                $customMessage.
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
        }

        $this->isAllSent($surveyNotifier);
        $this->em->flush();
    }

    private function sendEmail(SurveyNotifier $surveyNotifier)
    {
        $surveyId = $surveyNotifier->getSurvey()->getId();
        $content = $surveyNotifier->getEmailMessage();
        $subject = $surveyNotifier->getEmailSubject();
        foreach ($surveyNotifier->getSurveyNotifications() as $notification) {
            if ($notification->isSent()) {
                return;
            }
            $notification->setSent(true);
            $this->em->persist($notification);

            $notification->setTimeNotificationSent(new \DateTime());
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
                        'survey/survey_email_notification.html.twig',
                        array(
                            'firstname' => $notification->getUser()->getFirstName(),
                            'route' => $this->router->generate('survey_show_user_id', ['id' => $surveyId, 'userid'=>$identifier], RouterInterface::ABSOLUTE_URL),
                            'content' => $content,

                        )
                    ),
                    'text/html'
                );
            $this->mailer->send($message);
        }

        $this->isAllSent($surveyNotifier);
        $this->em->flush();
    }

    private function isAllSent(SurveyNotifier $surveyNotifier)
    {
        if ($surveyNotifier->isAllSent()) {
            return true;
        }

        foreach ($surveyNotifier->getSurveyNotifications() as $notification) {
            if (!$notification->isSent()) {
                return false;
            }
        }

        $surveyNotifier->setAllSent(true);
        $this->em->persist($surveyNotifier);
        $this->em->flush();
        return true;
    }
}
