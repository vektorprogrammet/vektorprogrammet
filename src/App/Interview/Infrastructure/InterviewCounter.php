<?php

namespace App\Interview\Infrastructure;

use App\Admission\Infrastructure\Entity\AdmissionPeriod;
use App\Admission\Infrastructure\Entity\Application;
use App\Interview\Infrastructure\Entity\InterviewDistribution;

class InterviewCounter
{
    public const YES = 'Ja';
    public const MAYBE = 'Kanskje';
    public const NO = 'Nei';

    /**
     * @param Application[] $applications
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

    /**
     * @param Application[] $applications
     *
     * @return array
     */
    public function createInterviewDistributions(array $applications, AdmissionPeriod $admissionPeriod)
    {
        $interviewDistributions = [];

        foreach ($applications as $application) {
            $interviewer = $application->getInterview()->getInterviewer();

            if (!array_key_exists($interviewer->__toString(), $interviewDistributions)) {
                $interviewDistributions[$interviewer->__toString()] = new InterviewDistribution($interviewer, $admissionPeriod);
            }
        }

        return array_values($interviewDistributions);
    }
}
