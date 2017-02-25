<?php

namespace AppBundle\Entity;

class InterviewDistribution
{
    private $user;
    private $semester;

    public function __construct(User $user, Semester $semester)
    {
        $this->user = $user;
        $this->semester = $semester;
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
        $interviews = $this->user->getInterviews();

        foreach ($interviews as $interview) {
            if ($interview->getApplication()->getSemester() !== $this->semester) {
                continue;
            }

            if (!$interview->getInterviewed()) {
                ++$interviewsLeftCount;
            }
        }

        return $interviewsLeftCount;
    }

    public function getTotalInterviews()
    {
        $interviewsCount = 0;
        $interviews = $this->user->getInterviews();

        foreach ($interviews as $interview) {
            if ($interview->getApplication()->getSemester() !== $this->semester) {
                continue;
            }

            ++$interviewsCount;
        }

        return $interviewsCount;
    }

    public function __toString()
    {
        return $this->user->__toString();
    }
}
