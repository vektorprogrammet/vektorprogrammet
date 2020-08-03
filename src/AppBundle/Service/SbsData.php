<?php

namespace AppBundle\Service;

use AppBundle\Entity\AdmissionPeriod;
use DateTime;

class SbsData extends ApplicationData
{
    public function getTotalApplicationsCount(): int
    {
        return $this->getApplicationCount();
    }
    public function getStep(): int
    {
        return (int) $this->getStepProgress();
    }

    public function getStepProgress(): float
    {
        return $this->determineCurrentStep($this->getAdmissionPeriod(), $this->getInterviewedAssistantsCount(), $this->getAssignedInterviewsCount(), $this->getTotalAssistantsCount());
    }

    public function getAdmissionTimeLeft(): int
    {
        return intval(ceil(($this->getAdmissionPeriod()->getAdmissionEndDate()->getTimestamp() - (new DateTime())->getTimestamp()) / 3600));
    }

    public function getTimeToAdmissionStart(): int
    {
        return intval(ceil(($this->getAdmissionPeriod()->getAdmissionStartDate()->getTimestamp() - (new DateTime())->getTimestamp()) / 3600));
    }

    private function determineCurrentStep(AdmissionPeriod $admissionPeriod, $interviewedAssistantsCount, $assignedInterviewsCount, $totalAssistantsCount): float
    {
        $today = new DateTime();
        if ($today > $admissionPeriod->getSemester()->getSemesterEndDate()) {
            return 0;
        }

        // Step 1 Wait for admission to start
        if ($this->admissionHasNotStartedYet($admissionPeriod)) {
            return 1 + ($today->format('U') - $admissionPeriod->getSemester()->getSemesterStartDate()->format('U')) / ($admissionPeriod->getAdmissionStartDate()->format('U') - $admissionPeriod->getSemester()->getSemesterStartDate()->format('U'));
        }

        // Step 2 Admission has started
        if ($admissionPeriod->hasActiveAdmission()) {
            return 2 + ($today->format('U') - $admissionPeriod->getAdmissionStartDate()->format('U')) / ($admissionPeriod->getAdmissionEndDate()->format('U') - $admissionPeriod->getAdmissionStartDate()->format('U'));
        }

        // Step 3 Interviewing
        // No interviews are assigned yet
        if ($assignedInterviewsCount == 0 && $interviewedAssistantsCount == 0) {
            return 3;
        } // There are interviews left to conduct
        elseif ($assignedInterviewsCount > 0) {
            return 3 + $interviewedAssistantsCount / ($assignedInterviewsCount + $interviewedAssistantsCount);
        }

        // Step 4 Distribute to schools
        // All interviews are conducted, but no one has been accepted yet
        if ($totalAssistantsCount == 0) {
            return 4;
        }

        // Step 5 Operating phase
        if ($this->admissionHasEnded($admissionPeriod)) {
            return 5 + ($today->format('U') - $admissionPeriod->getAdmissionEndDate()->format('U')) / ($admissionPeriod->getSemester()->getSemesterEndDate()->format('U') - $admissionPeriod->getAdmissionEndDate()->format('U'));
        }

        // Something is wrong
        return -1;
    }

    private function admissionHasNotStartedYet(AdmissionPeriod $admissionPeriod): bool
    {
        $today = new DateTime();

        return $today > $admissionPeriod->getSemester()->getSemesterStartDate() && $today < $admissionPeriod->getAdmissionStartDate();
    }

    private function admissionHasEnded(AdmissionPeriod $admissionPeriod): bool
    {
        $today = new DateTime();

        return $today < $admissionPeriod->getSemester()->getSemesterEndDate() && $today > $admissionPeriod->getAdmissionEndDate();
    }
}
