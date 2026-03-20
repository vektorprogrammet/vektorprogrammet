<?php

namespace App\Entity;

use App\Admission\Infrastructure\Entity\AdmissionPeriod;
use App\Admission\Infrastructure\Entity\Application;
use App\Type\InterviewStatusType;

class InterviewDistribution implements \Stringable
{
    private $interviews;
    private $totalCount;

    /**
     * InterviewDistribution constructor.
     */
    public function __construct(private readonly User $user, AdmissionPeriod $admissionPeriod)
    {
        $allInterviews = $this->filterInterviewsInSemester($this->user->getInterviews(), $admissionPeriod);
        $this->totalCount = count($allInterviews);
        $this->interviews = $this->filterNotInterviewed($allInterviews);
    }

    /**
     * @param Interview[]     $interviews
     * @param AdmissionPeriod $admissionPeriod
     *
     * @return Interview[]
     */
    private function filterInterviewsInSemester($interviews, $admissionPeriod)
    {
        return array_filter($interviews, fn (Interview $interview) => $interview->getApplication()->getAdmissionPeriod() === $admissionPeriod);
    }

    /**
     * @return Interview[]
     */
    private function filterNotInterviewed($interviews)
    {
        return array_filter($interviews, fn (Interview $interview) => !$interview->getInterviewed());
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    public function getInterviewsLeft()
    {
        $interviewsLeftCount = 0;

        foreach ($this->interviews as $interview) {
            if (!$interview->getCancelled() && !$interview->getInterviewed()) {
                ++$interviewsLeftCount;
            }
        }

        return $interviewsLeftCount;
    }

    public function getTotalInterviews()
    {
        return $this->totalCount;
    }

    public function countAccepted()
    {
        $interviews = array_filter($this->interviews, fn (Interview $interview) => $interview->getInterviewStatus() === InterviewStatusType::ACCEPTED);

        return count($interviews);
    }

    public function countCancelled()
    {
        $interviews = array_filter($this->interviews, fn (Interview $interview) => $interview->getInterviewStatus() === InterviewStatusType::CANCELLED);

        return count($interviews);
    }

    public function countPending()
    {
        $interviews = array_filter($this->interviews, fn (Interview $interview) => $interview->getInterviewStatus() === InterviewStatusType::PENDING);

        return count($interviews);
    }

    public function countNoContact()
    {
        $interviews = array_filter($this->interviews, fn (Interview $interview) => $interview->getInterviewStatus() === InterviewStatusType::NO_CONTACT);

        return count($interviews);
    }

    public function countRequestNewTime()
    {
        $interviews = array_filter($this->interviews, fn (Interview $interview) => $interview->getInterviewStatus() === InterviewStatusType::REQUEST_NEW_TIME);

        return count($interviews);
    }

    public function __toString(): string
    {
        return $this->user->__toString();
    }
}
