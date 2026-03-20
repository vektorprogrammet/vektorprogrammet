<?php

namespace App\Interview\Infrastructure;

use App\Admission\Infrastructure\Entity\AdmissionPeriod;
use App\Admission\Infrastructure\Entity\Application;
use App\Interview\Infrastructure\Entity\InterviewDistribution;

class InterviewDistributionFactory
{
    /**
     * @param Application[] $applications
     *
     * @return InterviewDistribution[]
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
