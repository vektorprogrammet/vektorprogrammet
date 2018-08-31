<?php

namespace AppBundle\Service;

use AppBundle\Entity\Application;
use AppBundle\Entity\Interview;
use AppBundle\Entity\InterviewAnswer;
use AppBundle\Entity\User;
use AppBundle\Mailer\MailerInterface;
use AppBundle\Role\Roles;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class InterviewManager
{
    private $tokenStorage;
    private $authorizationChecker;
    private $mailer;
    private $twig;
    private $logger;
    private $em;

    /**
     * InterviewManager constructor.
     *
     * @param TokenStorage                  $tokenStorage
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param MailerInterface               $mailer
     * @param \Twig_Environment             $twig
     * @param LoggerInterface               $logger
     * @param EntityManager                 $em
     */
    public function __construct(TokenStorage $tokenStorage, AuthorizationCheckerInterface $authorizationChecker, MailerInterface $mailer, \Twig_Environment $twig, LoggerInterface $logger, EntityManager $em)
    {
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->logger = $logger;
        $this->em = $em;
    }

    /**
     * Only team leader and above, or the assigned interviewer should be able to see the interview.
     *
     * @param Interview $interview
     *
     * @return bool
     */
    public function loggedInUserCanSeeInterview(Interview $interview): bool
    {
        $user = $this->tokenStorage->getToken()->getUser();

        return $this->authorizationChecker->isGranted(Roles::TEAM_LEADER) ||
               $interview->isInterviewer($user) ||
               $interview->isCoInterviewer($user);
    }

    /**
     * @param Interview $interview
     *
     * @return Interview
     */
    public function initializeInterviewAnswers(Interview $interview)
    {
        $existingAnswers = $interview->getInterviewAnswers();
        if (!is_array($existingAnswers)) {
            $existingAnswers = $existingAnswers->toArray();
        }

        $existingQuestions = array_map(function (InterviewAnswer $interviewAnswer) {
            return $interviewAnswer->getInterviewQuestion();
        }, $existingAnswers);

        foreach ($interview->getInterviewSchema()->getInterviewQuestions() as $interviewQuestion) {
            $interviewAlreadyHasQuestion = array_search($interviewQuestion, $existingQuestions) !== false;
            if ($interviewAlreadyHasQuestion) {
                continue;
            }

            $answer = new InterviewAnswer();
            $answer->setInterview($interview);
            $answer->setInterviewQuestion($interviewQuestion);

            $interview->addInterviewAnswer($answer);
        }

        return $interview;
    }

    /**
     * @param User        $interviewer
     * @param Application $application
     */
    public function assignInterviewerToApplication(User $interviewer, Application $application)
    {
        $interview = $application->getInterview();
        if (!$interview) {
            $interview = new Interview();
            $application->setInterview($interview);
        }
        $interview->setInterviewed(false);
        $interview->setUser($application->getUser());
        $interview->setInterviewer($interviewer);
    }

    /**
     * @param Interview $interview
     * @param array     $data
     */
    public function sendScheduleEmail(Interview $interview, array $data)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Intervju for vektorprogrammet')
            ->setFrom(array('opptak@vektorprogrammet.no' => 'Vektorprogrammet'))
            ->setTo($data['to'])
            ->setReplyTo($data['from'])
            ->setBody(
                $this->twig->render('interview/email.html.twig',
                    array('message' => $data['message'],
                        'datetime' => $data['datetime'],
                        'room' => $data['room'],
                        'campus' => $data['campus'],
                        'mapLink' => $data['mapLink'],
                        'fromName' => $interview->getInterviewer()->getFirstName().' '.$interview->getInterviewer()->getLastName(),
                        'fromMail' => $data['from'],
                        'fromPhone' => $interview->getInterviewer()->getPhone(),
                        'responseCode' => $interview->getResponseCode(),
                    )
                ),
                'text/html'
            );
        $this->mailer->send($message);
    }

    /**
     * @param Interview $interview
     */
    public function sendRescheduleEmail(Interview $interview)
    {
        $application = $this->em->getRepository('AppBundle:Application')->findOneBy(array('interview' => $interview));

        $message = \Swift_Message::newInstance()
            ->setSubject('Intervju: Ønske om ny tid')
            ->setTo($interview->getInterviewer()->getEmail())
            ->setFrom(array('opptak@vektorprogrammet.no' => 'Vektorprogrammet'))
            ->setReplyTo('opptak@vektorprogrammet.no')
            ->setBody(
                $this->twig->render('interview/reschedule_email.html.twig',
                    array('interview' => $interview,
                        'application' => $application,
                    )
                ),
                'text/html'
            );

        $this->mailer->send($message);
    }

    /**
     * @param Interview $interview
     */
    public function sendCancelEmail(Interview $interview)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Intervju: Kansellert')
            ->setTo($interview->getInterviewer()->getEmail())
            ->setFrom(array('opptak@vektorprogrammet.no' => 'Vektorprogrammet'))
            ->setReplyTo('opptak@vektorprogrammet.no')
            ->setBody(
                $this->twig->render('interview/cancel_email.html.twig',
                    array('interview' => $interview,
                    )
                ),
                'text/html'
            );

        $this->mailer->send($message);
    }

    /**
     * @param Interview $interview
     *
     * @return array
     */
    public function getDefaultScheduleFormData(Interview $interview): array
    {
        $message = "Hei, {$interview->getUser()->getFirstName()}!
         
Vi har satt opp et intervju for deg angående opptak til vektorprogrammet. 
Vennligst gi beskjed til meg hvis tidspunktet ikke passer.";

        return array(
            'datetime' => $interview->getScheduled(),
            'room' => $interview->getRoom(),
            'campus' => $interview->getCampus(),
            'mapLink' => $interview->getMapLink(),
            'message' => $message,
            'from' => $interview->getInterviewer()->getEmail(),
            'to' => $interview->getUser()->getEmail(),

        );
    }
}
