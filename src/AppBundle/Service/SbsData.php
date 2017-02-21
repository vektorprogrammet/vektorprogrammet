<?php

namespace AppBundle\Service;

use AppBundle\Entity\Semester;

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
        return $this->determineCurrentStep($this->getSemester(), $this->getInterviewedAssistantsCount(), $this->getAssignedInterviewsCount(), $this->getTotalAssistantsCount());
    }

    public function getAdmissionTimeLeft(): int
    {
        return intval(ceil(($this->getSemester()->getAdmissionEndDate()->getTimestamp() - (new \DateTime())->getTimestamp()) / 3600));
    }

    public function getTimeToAdmissionStart(): int
    {
        return intval(ceil(($this->getSemester()->getAdmissionStartDate()->getTimestamp() - (new \DateTime())->getTimestamp()) / 3600));
    }

    private function determineCurrentStep(Semester $semester, $interviewedAssistantsCount, $assignedInterviewsCount, $totalAssistantsCount): float
    {
        $today = new \DateTime();
        if ($today > $semester->getSemesterEndDate()) {
            return 0;
        }

        // Step 1 Wait for admission to start
        if ($this->admissionHasNotStartedYet($semester)) {
            return 1 + ($today->format('U') - $semester->getSemesterStartDate()->format('U')) / ($semester->getAdmissionStartDate()->format('U') - $semester->getSemesterStartDate()->format('U'));
        }

        // Step 2 Admission has started
        if ($this->admissionIsOngoing($semester)) {
            return 2 + ($today->format('U') - $semester->getAdmissionStartDate()->format('U')) / ($semester->getAdmissionEndDate()->format('U') - $semester->getAdmissionStartDate()->format('U'));
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
        if ($this->admissionHasEnded($semester)) {
            return 5 + ($today->format('U') - $semester->getAdmissionEndDate()->format('U')) / ($semester->getSemesterEndDate()->format('U') - $semester->getAdmissionEndDate()->format('U'));
        }

        // Something is wrong
        return -1;
    }

    private function admissionHasNotStartedYet(Semester $semester): bool
    {
        $today = new \DateTime();

        return $today > $semester->getSemesterStartDate() && $today < $semester->getAdmissionStartDate();
    }

    private function admissionIsOngoing(Semester $semester): bool
    {
        $today = new \DateTime();

        return $today > $semester->getAdmissionStartDate() && $today < $semester->getAdmissionEndDate();
    }

    private function admissionHasEnded(Semester $semester): bool
    {
        $today = new \DateTime();

        return $today < $semester->getSemesterEndDate() && $today > $semester->getAdmissionEndDate();
    }
}
