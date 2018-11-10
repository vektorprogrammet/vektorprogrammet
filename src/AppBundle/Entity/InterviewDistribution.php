<?php

namespace AppBundle\Entity;

use AppBundle\Type\InterviewStatusType;

class InterviewDistribution
{
    private $user;
    private $interviews;
    private $totalCount;

    /**
     * InterviewDistribution constructor.
     *
     * @param User $user
     * @param AdmissionPeriod $admissionPeriod
     */
    public function __construct(User $user, AdmissionPeriod $admissionPeriod)
    {
        $this->user = $user;
        $allInterviews = $this->filterInterviewsInSemester($this->user->getInterviews(), $admissionPeriod);
        $this->totalCount = count($allInterviews);
        $this->interviews = $this->filterNotInterviewed($allInterviews);
    }

    /**
     * @param Interview[] $interviews
     * @param AdmissionPeriod $admissionPeriod
     *
     * @return Interview[]
     */
    private function filterInterviewsInSemester($interviews, $admissionPeriod)
    {
        return array_filter($interviews, function (Interview $interview) use ($admissionPeriod) {
            return $interview->getApplication()->getAdmissionPeriod() === $admissionPeriod;
        });
    }

    /**
     * @param $interviews
     *
     * @return Interview[]
     */
    private function filterNotInterviewed($interviews)
    {
        return array_filter($interviews, function (Interview $interview) {
            return !$interview->getInterviewed();
        });
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
        $interviews = array_filter($this->interviews, function (Interview $interview) {
            return $interview->getInterviewStatus() === InterviewStatusType::ACCEPTED;
        });

        return count($interviews);
    }

    public function countCancelled()
    {
        $interviews = array_filter($this->interviews, function (Interview $interview) {
            return $interview->getInterviewStatus() === InterviewStatusType::CANCELLED;
        });

        return count($interviews);
    }

    public function countPending()
    {
        $interviews = array_filter($this->interviews, function (Interview $interview) {
            return $interview->getInterviewStatus() === InterviewStatusType::PENDING;
        });

        return count($interviews);
    }

    public function countNoContact()
    {
        $interviews = array_filter($this->interviews, function (Interview $interview) {
            return $interview->getInterviewStatus() === InterviewStatusType::NO_CONTACT;
        });

        return count($interviews);
    }

    public function countRequestNewTime()
    {
        $interviews = array_filter($this->interviews, function (Interview $interview) {
            return $interview->getInterviewStatus() === InterviewStatusType::REQUEST_NEW_TIME;
        });

        return count($interviews);
    }

    public function __toString()
    {
        return $this->user->__toString();
    }
}
