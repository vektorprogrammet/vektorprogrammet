<?php

namespace AppBundle\Service;

use AppBundle\Entity\Interview;
use AppBundle\Entity\InterviewAnswer;

class InterviewManager
{
    public function __construct()
    {
    }

    public function initializeInterviewAnswers(Interview $interview)
    {
        foreach ($interview->getInterviewSchema()->getInterviewQuestions() as $interviewQuestion) {
            // Create a new answer object for the question
            $answer = new InterviewAnswer();
            $answer->setInterview($interview);
            $answer->setInterviewQuestion($interviewQuestion);

            // Add the answer object to the interview
            $interview->addInterviewAnswer($answer);
        }
    }
}
