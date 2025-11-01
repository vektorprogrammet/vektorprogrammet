<?php

namespace App\Repository\Eloquent;

use App\Models\AdmissionPeriod;
use App\Models\Interview;
use App\Models\Semester;
use App\Models\User;
use App\Repository\Contract\InterviewRepositoryInterface;
use Carbon\Carbon;
use DateTime;

/**
 * Eloquent implementation of InterviewRepositoryInterface.
 */
class InterviewRepository implements InterviewRepositoryInterface
{
    /**
     * Find last scheduled interview by user in admission period.
     *
     * @param User $user
     * @param AdmissionPeriod $admissionPeriod
     * @return Interview|null
     */
    public function findLastScheduledByUserInAdmissionPeriod(User $user, AdmissionPeriod $admissionPeriod): ?Interview
    {
        return Interview::query()
            ->whereHas('application', function ($query) use ($admissionPeriod) {
                $query->where('admission_period_id', $admissionPeriod->id);
            })
            ->where('interviewer_id', $user->id)
            ->whereNotNull('last_schedule_changed')
            ->orderBy('last_schedule_changed', 'desc')
            ->first();
    }

    /**
     * Find all interviewed interviews by semester.
     *
     * @param Semester $semester
     * @return Interview[]
     */
    public function findAllInterviewedInterviewsBySemester(Semester $semester): array
    {
        return Interview::query()
            ->where('interviewed', true)
            ->whereHas('application.admissionPeriod', function ($query) use ($semester) {
                $query->where('semester_id', $semester->id);
            })
            ->get()
            ->toArray();
    }

    /**
     * Get number of interviews by user in semester.
     *
     * @param User $user
     * @param Semester $semester
     * @return int
     */
    public function numberOfInterviewsByUserInSemester(User $user, Semester $semester): int
    {
        return Interview::query()
            ->where('interviewer_id', $user->id)
            ->whereHas('application.admissionPeriod', function ($query) use ($semester) {
                $query->where('semester_id', $semester->id);
            })
            ->whereHas('application', function ($query) {
                $query->where('previous_participation', false);
            })
            ->count();
    }

    /**
     * Find latest interview by user.
     *
     * @param User $user
     * @return Interview|null
     */
    public function findLatestInterviewByUser(User $user): ?Interview
    {
        return Interview::query()
            ->where('interviewer_id', $user->id)
            ->orderBy('scheduled', 'desc')
            ->first();
    }


    /**
     * Find interview by response code.
     *
     * @param string $responseCode
     * @return Interview|null
     */
    public function findByResponseCode(string $responseCode): ?Interview
    {
        return Interview::where('response_code', $responseCode)->first();
    }

    /**
     * Find uncompleted interviews by interviewer in current semester.
     *
     * @param User $interviewer
     * @return Interview[]
     */
    public function findUncompletedInterviewsByInterviewerInCurrentSemester(User $interviewer): array
    {
        $department = $interviewer->getDepartment();
        if (!$department) {
            return [];
        }

        $admissionPeriod = $department->getCurrentAdmissionPeriod();
        if (!$admissionPeriod || !$admissionPeriod->semester) {
            return [];
        }

        $semester = $admissionPeriod->semester;

        return Interview::query()
            ->where('interviewed', false)
            ->where(function ($query) use ($interviewer) {
                $query->where('interviewer_id', $interviewer->id)
                      ->orWhere('co_interviewer_id', $interviewer->id);
            })
            ->whereHas('application.admissionPeriod', function ($query) use ($semester) {
                $query->where('semester_id', $semester->id);
            })
            ->orderBy('scheduled', 'asc')
            ->get()
            ->toArray();
    }

    /**
     * Find interviewers in semester.
     *
     * @param Semester $semester
     * @return User[]
     */
    public function findInterviewersInSemester(Semester $semester): array
    {
        $interviews = Interview::query()
            ->whereHas('application.admissionPeriod', function ($query) use ($semester) {
                $query->where('semester_id', $semester->id);
            })
            ->with(['interviewer', 'coInterviewer'])
            ->get();

        $interviewers = [];
        foreach ($interviews as $interview) {
            if ($interview->interviewer) {
                $interviewers[] = $interview->interviewer;
            }
            if ($interview->coInterviewer) {
                $interviewers[] = $interview->coInterviewer;
            }
        }

        // Remove duplicates based on ID
        $uniqueInterviewers = [];
        $seenIds = [];
        foreach ($interviewers as $interviewer) {
            if (!in_array($interviewer->id, $seenIds)) {
                $uniqueInterviewers[] = $interviewer;
                $seenIds[] = $interviewer->id;
            }
        }

        return $uniqueInterviewers;
    }

    /**
     * Find interviews which will receive accept-interview notifications.
     * All interviews scheduled to a time after $time and having PENDING
     * interview status apply.
     *
     * @param DateTime $time
     * @return Interview[]
     */
    public function findAcceptInterviewNotificationRecipients(DateTime $time): array
    {
        $carbonTime = Carbon::instance($time);

        return Interview::query()
            ->where('scheduled', '>', $carbonTime)
            ->where('interview_status', 'PENDING')
            ->get()
            ->toArray();
    }
}

