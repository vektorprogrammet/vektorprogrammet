<?php


namespace AppBundle\Service;

use AppBundle\Entity\SurveyNotification;
use AppBundle\Entity\SurveyNotifier;
use AppBundle\Entity\SurveyTaken;
use AppBundle\Mailer\Mailer;
use AppBundle\Sms\Sms;
use AppBundle\Sms\SmsSenderInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
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


    /**
     * SurveyNotifierManager constructor.
     * @param Mailer $mailer
     * @param \Twig_Environment $twig
     * @param LoggerInterface $logger
     * @param EntityManager $em
     * @param RouterInterface $router
     * @param SmsSenderInterface $smsSender
     */
    public function __construct(Mailer $mailer, \Twig_Environment $twig, LoggerInterface $logger, EntityManager $em, RouterInterface $router, SmsSenderInterface $smsSender)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->logger = $logger;
        $this->em = $em;
        $this->router = $router;
        $this->smsSender = $smsSender;
    }
    public function initializeSurveyNotifier(SurveyNotifier $surveyNotifier)
    {
        $this->em->persist($surveyNotifier);
        $survey = $surveyNotifier->getSurvey();
        $users = $surveyNotifier->getUserGroup()->getUsers();

        $notifications = array();
        foreach ($users as $user) {
            $isSurveyTakenByUser = !empty($this->em->getRepository(SurveyTaken::class)->findAllBySurveyAndUser($survey, $user));
            if ($isSurveyTakenByUser) {
                continue;
            }
            $notification = new SurveyNotification();
            $notification->setUser($user);
            $notification->setSurveyNotifier($surveyNotifier);
            $notifications[] = $notification;
            $this->em->persist($notification);
        }
        $surveyNotifier->getUserGroup()->setIsActive(true);
        $this->em->persist($surveyNotifier->getUserGroup());
        $this->em->flush();
    }

    public function sendNotifications(SurveyNotifier $surveyNotifier)
    {
        $this->isAllSent($surveyNotifier);
        $surveyNotifier->setIsActive(true);
        $surveyNotifier->getUserGroup()->setIsActive(true);
        $surveyNotifier->getUserGroup()->getUserGroupCollection()->setIsActive(true);
        $this->em->persist($surveyNotifier);
        $this->em->flush();

        if ($surveyNotifier->isAllSent()){
            return;
        } elseif ($surveyNotifier->getNotificationType() === SurveyNotifier::$SMS_NOTIFICATION) {
            $this->sendSMS($surveyNotifier);
        } elseif ($surveyNotifier->getNotificationType() === SurveyNotifier::$EMAIL_NOTIFICATION) {
            $this->sendEmail($surveyNotifier);
        }
    }
    ## TODO : REMOVE DATETIME PICKER

    ## TODO: REWRITE MESSAGES

    ## TODO : FIND OUT HOW THIS IS TESTED

    ## TODO: MAKE SURE THAT THIS WORKS

    private function sendSMS(SurveyNotifier $surveyNotifier)
    {
        $surveyid = $surveyNotifier->getSurvey()->getId();
        foreach ($surveyNotifier->getSurveyNotifications() as $notification) {
            if ($notification->isSent()) return;
            $notification->setIsSent(true);
            $this->em->persist($notification);
            $this->em->flush();


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
                "Vi i Vektor takker for et semester med deg som assistent. \n" .

                "Vi jobber kontinuerlig for å forbedre oss og er avhengig av tilbakemelding. Svar på den følgende korte undersøkelsen og vær med i trekningen av flotte premier \n" .

                $this->router->generate(
                    'survey_show_assistant_id',
                    ['id' => $surveyid, 'userid'=>$identifier],
                    RouterInterface::ABSOLUTE_URL
                ) .
                "\n\n" .
                "Med vennlig hilsen\n" .
                "oss i Vektor";

            $sms = new Sms();
            $sms->setMessage($message);
            $sms->setSender("Vektor");
            $sms->setRecipients([$phoneNumber]);
            $this->smsSender->send($sms);
        }

        $this->isAllSent($surveyNotifier);

    }

    private function sendEmail(SurveyNotifier $surveyNotifier)
    {
        $fromEmail = 'amir@vektorprogrammet.no'; ## TODO : CREATE TO EMAIL
        $surveyid = $surveyNotifier->getSurvey()->getId();
        foreach ($surveyNotifier->getSurveyNotifications() as $notification) {
            if ($notification->isSent()) return;
            $notification->setIsSent(true);
            $this->em->persist($notification);
            $this->em->flush();

            $notification->setTimeNotificationSent(new \DateTime());
            $user = $notification->getUser();
            $identifier = $notification->getUserIdentifier();
            $email = $user->getEmail();

            $message = (new \Swift_Message())
                ->setSubject('Takk for et å')
                ->setTo($email)
                ->setReplyTo($fromEmail)
                ->setBody(
                    $this->twig->render(
                        'survey/survey_email_notification.html.twig',
                        array(
                            'route' => $this->router->generate('survey_show_assistant_id', ['id' => $surveyid, 'userid'=>$identifier], RouterInterface::ABSOLUTE_URL)
                            )

                    ),
                    'text/html'
                );
            $this->mailer->send($message);
        }

        $this->isAllSent($surveyNotifier);
    }

    private function isAllSent(SurveyNotifier $surveyNotifier){
        if ($surveyNotifier->isAllSent()) return true;

        foreach ($surveyNotifier->getSurveyNotifications() as $notification){
            if (!$notification->isSent()) return false;
        }

        $surveyNotifier->setIsAllSent(true);
        $this->em->persist($surveyNotifier);
        $this->em->flush();
        return true;

    }
}
