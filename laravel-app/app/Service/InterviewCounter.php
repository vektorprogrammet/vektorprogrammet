<?php

namespace App\Service;

use App\Models\AdmissionPeriod;
use App\Models\Application;
use App\Models\InterviewDistribution;
use App\Service\Contract\InterviewCounterInterface;

class InterviewCounter implements InterviewCounterInterface
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
    public function count(array $applications, string $suitable): int
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
     * @param AdmissionPeriod $admissionPeriod
     *
     * @return array
     */
    public function createInterviewDistributions(array $applications, AdmissionPeriod $admissionPeriod): array
    {
        $interviewDistributions = array();

        foreach ($applications as $application) {
            $interviewer = $application->getInterview()->getInterviewer();

            if (!array_key_exists($interviewer->__toString(), $interviewDistributions)) {
                $interviewDistributions[$interviewer->__toString()] = new InterviewDistribution($interviewer, $admissionPeriod);
            }
        }

        return array_values($interviewDistributions);
    }
}
