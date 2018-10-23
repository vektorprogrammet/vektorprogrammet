<?php

namespace AppBundle\Service;

use AppBundle\Entity\Application;
use AppBundle\Entity\Interview;
use AppBundle\Entity\InterviewAnswer;
use AppBundle\Entity\Semester;
use AppBundle\Entity\User;
use AppBundle\Mailer\MailerInterface;
use AppBundle\Role\Roles;
use AppBundle\Type\InterviewStatusType;
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
            ->setTo($data['to'])
            ->setReplyTo($data['from'])
            ->setBody(
                $this->twig->render(
                    'interview/email.html.twig',
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
        $user = $interview->getUser();

        $message = \Swift_Message::newInstance()
            ->setSubject("[$user] Intervju: Ønske om ny tid")
            ->setTo($interview->getInterviewer()->getEmail())
            ->setBody(
                $this->twig->render(
                    'interview/reschedule_email.html.twig',
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
        $user = $interview->getUser();
        $message = \Swift_Message::newInstance()
            ->setSubject("[$user] Intervju: Kansellert")
            ->setTo($interview->getInterviewer()->getEmail())
            ->setBody(
                $this->twig->render(
                    'interview/cancel_email.html.twig',
                    array('interview' => $interview,
                    )
                ),
                'text/html'
            );

        $this->mailer->send($message);
    }

    public function sendInterviewScheduleToInterviewer(User $interviewer)
    {
        $interviews = $this->em->getRepository('AppBundle:Interview')->findUncompletedInterviewsByInterviewerInCurrentSemester($interviewer);

        $nothingMoreToDo = true;
        foreach ($interviews as $interview) {
            $status = $interview->getInterviewStatus();

            if ($status === InterviewStatusType::NO_CONTACT ||
                 $status === InterviewStatusType::PENDING ||
                 $status === InterviewStatusType::REQUEST_NEW_TIME ||
                 $status === InterviewStatusType::ACCEPTED
            ) {
                $nothingMoreToDo = false;
                break;
            }
        }

        if ($nothingMoreToDo) {
            return;
        }

        $message = \Swift_Message::newInstance()
             ->setSubject('Dine intervjuer dette semesteret')
             ->setTo($interviewer->getEmail())
             ->setBody(
                 $this->twig->render(
                     'interview/schedule_of_interviews_email.html.twig',
                     array(
                         'interviews'  => $interviews,
                         'interviewer' => $interviewer
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
        $previousScheduledInterview = $this->em->getRepository('AppBundle:Interview')
                                               ->findLastScheduledByUserInSemester($interview->getInterviewer(), $interview->getApplication()->getSemester());
        $room = null;
        $campus = null;
        $mapLink = null;
        if ($previousScheduledInterview) {
            $room = $previousScheduledInterview->getRoom();
            $campus = $previousScheduledInterview->getCampus();
            $mapLink = $previousScheduledInterview->getMapLink();
        }


        $message = "Hei, {$interview->getUser()->getFirstName()}!
         
Vi har satt opp et intervju for deg angående opptak til vektorprogrammet. 
Vennligst gi beskjed til meg hvis tidspunktet ikke passer.";

        return array(
            'datetime' => $interview->getScheduled(),
            'room' => $interview->getRoom() ?: $room,
            'campus' => $interview->getCampus() ?: $campus,
            'mapLink' => $interview->getMapLink() ?: $mapLink,
            'message' => $message,
            'from' => $interview->getInterviewer()->getEmail(),
            'to' => $interview->getUser()->getEmail(),

        );
    }
}
