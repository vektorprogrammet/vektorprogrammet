<?php

namespace AppBundle\Service;

use AppBundle\Entity\Application;

class InterviewCounter
{
    const YES = 'Ja';
    const MAYBE = 'Kanskje';
    const NO = 'Nei';

    /**
     * @param Application[] $applications
     * @param string        $suitable
     *
     * @return int
     */
    public function count(array $applications, string $suitable)
    {
        $count = 0;

        foreach ($applications as $application) {
            $interview = $application->getInterview();
            if ($interview === null) {
                continue;
            }

            $suitableAssistant = $interview->getInterviewScore()->getSuitableAssistant();
            if ($suitableAssistant === $suitable) {
                ++$count;
            }
        }

        return $count;
    }
}
