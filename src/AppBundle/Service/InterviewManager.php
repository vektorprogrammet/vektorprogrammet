<?php

namespace AppBundle\Service;

use AppBundle\Entity\Application;
use AppBundle\Entity\Interview;
use AppBundle\Entity\InterviewAnswer;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class InterviewManager
{
    private $tokenStorage;
    private $authorizationChecker;
    private $mailer;
    private $twig;

    /**
     * InterviewManager constructor.
     *
     * @param TokenStorage                  $tokenStorage
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param \Swift_Mailer                 $mailer
     * @param \Twig_Environment             $twig
     */
    public function __construct(TokenStorage $tokenStorage, AuthorizationCheckerInterface $authorizationChecker, \Swift_Mailer $mailer, \Twig_Environment $twig)
    {
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function prepareInterview(Application $application)
    {
        $interview = $application->getInterview();

        // Only admin and above, or the assigned interviewer should be able to conduct an interview
        if (!$this->loggedInUserCanSeeInterview($interview)) {
            throw new AccessDeniedException();
        }

        $this->initializeInterviewAnswers($interview);

        return $interview;
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

        return $this->authorizationChecker->isGranted(RoleManager::ROLE_TEAM_LEADER) || $interview->isInterviewer($user);
    }

    public function initializeInterviewAnswers(Interview $interview)
    {
        if ($interview->getInterviewed()) {
            return;
        }

        foreach ($interview->getInterviewSchema()->getInterviewQuestions() as $interviewQuestion) {
            // Create a new answer object for the question
            $answer = new InterviewAnswer();
            $answer->setInterview($interview);
            $answer->setInterviewQuestion($interviewQuestion);

            // Add the answer object to the interview
            $interview->addInterviewAnswer($answer);
        }
    }

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
                        'fromName' => $interview->getInterviewer()->getFirstName().' '.$interview->getInterviewer()->getLastName(),
                        'fromMail' => $data['from'],
                        'fromPhone' => $interview->getInterviewer()->getPhone(),
                    )
                ),
                'text/html'
            );
        $this->mailer->send($message);
    }

    public function getDefaultScheduleFormData(Interview $interview): array
    {
        $message = "Hei, {$interview->getUser()->getFirstName()}!
         
Vi har satt opp et intervju for deg angående opptak til vektorprogrammet. 
Vennligst gi beskjed til meg hvis tidspunktet ikke passer.";

        return array(
            'datetime' => $interview->getScheduled(),
            'message' => $message,
            'from' => $interview->getInterviewer()->getEmail(),
            'to' => $interview->getUser()->getEmail(),
        );
    }
}
