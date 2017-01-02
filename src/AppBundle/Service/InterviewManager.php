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

    /**
     * InterviewManager constructor.
     *
     * @param TokenStorage                  $tokenStorage
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(TokenStorage $tokenStorage, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
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
}
