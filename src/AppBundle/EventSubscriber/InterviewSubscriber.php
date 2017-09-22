<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Event\InterviewConductedEvent;
use AppBundle\Event\InterviewEvent;
use AppBundle\Service\InterviewManager;
use AppBundle\Service\InterviewNotificationManager;
use AppBundle\Service\SbsData;
use AppBundle\Sms\Sms;
use AppBundle\Sms\SmsSender;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;

class InterviewSubscriber implements EventSubscriberInterface
{
    private $mailer;
    private $twig;
    private $session;
    private $logger;
    private $sbsData;
    private $notificationManager;
    private $interviewManager;
    private $smsSender;
    private $router;

    public function __construct(
        \Swift_Mailer $mailer,
        \Twig_Environment $twig,
        Session $session,
        LoggerInterface $logger,
        SbsData $sbsData,
        InterviewNotificationManager $notificationManager,
        InterviewManager $interviewManager,
        SmsSender $smsSender,
        RouterInterface $router
    ) {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->session = $session;
        $this->logger = $logger;
        $this->sbsData = $sbsData;
        $this->notificationManager = $notificationManager;
        $this->interviewManager = $interviewManager;
        $this->smsSender = $smsSender;
        $this->router = $router;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            InterviewConductedEvent::NAME => array(
                array('logEvent', 2),
                array('sendSlackNotifications', 1),
                array('sendInterviewReceipt', 0),
                array('addFlashMessage', -1),
            ),
            InterviewEvent::SCHEDULE => array(
                array('sendScheduleEmail', 0),
                array('sendScheduleSms', 0),
            ),
        );
    }

    public function sendInterviewReceipt(InterviewConductedEvent $event)
    {
        $application = $event->getApplication();
        $interviewer = $application->getInterview()->getInterviewer();

        // Send email to the interviewee with a summary of the interview
        $emailMessage = \Swift_Message::newInstance()
            ->setSubject('Vektorprogrammet intervju')
            ->setFrom(array('rekruttering@vektorprogrammet.no' => 'Vektorprogrammet'))
            ->setTo($application->getUser()->getEmail())
            ->setReplyTo($interviewer->getEmail())
            ->setBody($this->twig->render('interview/interview_summary_email.html.twig', array(
                'application' => $application,
                'interviewer' => $interviewer,
            )));
        $this->mailer->send($emailMessage);

        $this->logger->info("Interview receipt sent to {$application->getUser()} at {$application->getUser()->getEmail()}");
    }

    public function addFlashMessage(InterviewConductedEvent $event)
    {
        $user = $event->getApplication()->getUser();
        $message = "Intervjuet med $user ble lagret. En kvittering med et sammendrag av praktisk informasjon fra intervjuet blir sendt til {$user->getEmail()}.";

        $this->session->getFlashBag()->add('success', $message);
    }

    public function logEvent(InterviewConductedEvent $event)
    {
        $application = $event->getApplication();

        $interviewee = $application->getUser();

        $department = $interviewee->getDepartment();

        $this->logger->info("$department: New interview with $interviewee registered");
    }

    public function sendSlackNotifications(InterviewConductedEvent $event)
    {
        $application = $event->getApplication();

        $department = $application->getUser()->getDepartment();

        if ($this->sbsData->getInterviewedAssistantsCount() === 10 || $this->sbsData->getInterviewedAssistantsCount() % 25 === 0) {
            $this->notificationManager->sendApplicationCountNotification($department);
        }

        if ($this->sbsData->applicantsNotYetInterviewedCount() <= 0 && $this->sbsData->getStep() >= 4) {
            $this->notificationManager->sendInterviewsCompletedNotification($department);
        }
    }

    public function sendScheduleEmail(InterviewEvent $event)
    {
        $this->interviewManager->sendScheduleEmail($event->getInterview(), $event->getData());
    }

    public function sendScheduleSms(InterviewEvent $event)
    {
        $interview = $event->getInterview();
        $data = $event->getData();
        $user = $interview->getUser();
        $phoneNumber = $interview->getUser()->getPhone();
        $interviewer = $interview->getInterviewer();

        $validNumber = $this->smsSender->validatePhoneNumber($phoneNumber);
        if (!$validNumber) {
            $this->logger->alert("Kunne ikke sende schedule sms til *$user*\n Tlf.nr.: *$phoneNumber*");
            return;
        }

        $message =
            $data['message'] .
            "\n\n" .
            "Tid: ".$data['datetime']->format('d.m.Y - H:i') .
            "\n" .
            "Rom: ".$data['room'] .
            "\n\n" .
            "Vennligst følg linken under for å godkjenne tidspunktet eller be om ny tid:\n" .
            $this->router->generate('interview_response',
                ['responseCode' => $interview->getResponseCode()],
                RouterInterface::ABSOLUTE_URL
            ) .
            "\n\n" .
            "Mvh $interviewer, Vektorprogrammet\n" .
            $interviewer->getEmail() .
            "\n" .
            $interviewer->getPhone();

        $sms = new Sms();
        $sms->message($message);
        $sms->sender("Vektor");
        $sms->recipients([$interview->getUser()->getPhone()]);

        $this->smsSender->send($sms);
    }
}
