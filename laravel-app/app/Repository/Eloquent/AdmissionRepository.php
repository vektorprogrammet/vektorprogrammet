<?php

namespace App\Repository\Eloquent;

use App\Models\Admission;
use App\Models\Application;
use App\Models\ApplicationStatistic;
use App\Models\Department;
use App\Models\Semester;
use App\Models\User;
use App\Repository\Contract\AdmissionRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Eloquent implementation of AdmissionRepositoryInterface.
 * 
 * Note: In Doctrine, "Admission" appears to be an alias or mapping for "Application".
 * This implementation uses the Application model for Admission queries.
 * ApplicationStatistic is used for statistical queries.
 */
class AdmissionRepository implements AdmissionRepositoryInterface
{
    /**
     * Find interviewed applicants.
     *
     * @param Department|null $department
     * @param Semester|null $semester
     * @return Admission[]
     */
    public function findInterviewedApplicants($department = null, $semester = null): array
    {
        $query = Application::query()
            ->join('application_statistic', 'application.id', '=', 'application_statistic.application_id')
            ->join('semester', 'application_statistic.semester_id', '=', 'semester.id')
            ->join('department', 'semester.department_id', '=', 'department.id')
            ->join('interview', 'application.id', '=', 'interview.application_id')
            ->where('interview.interviewed', true);

        if ($department !== null) {
            $departmentId = $department instanceof Department ? $department->id : $department;
            $query->where('department.id', $departmentId);
        }

        if ($semester !== null) {
            $semesterId = $semester instanceof Semester ? $semester->id : $semester;
            $query->where('semester.id', $semesterId);
        }

        return $query->orderBy('application.user_created', 'asc')
            ->select('application.*')
            ->get()
            ->toArray();
    }

    /**
     * Find interviewed applicants by interviewer.
     *
     * @param Department $department
     * @param Semester $semester
     * @param User $interviewer
     * @return Admission[]
     */
    public function findInterviewedApplicantsByInterviewer($department, $semester, $interviewer): array
    {
        $departmentId = $department instanceof Department ? $department->id : $department;
        $semesterId = $semester instanceof Semester ? $semester->id : $semester;
        $interviewerId = $interviewer instanceof User ? $interviewer->id : $interviewer;

        return Application::query()
            ->join('application_statistic', 'application.id', '=', 'application_statistic.application_id')
            ->join('semester', 'application_statistic.semester_id', '=', 'semester.id')
            ->join('department', 'semester.department_id', '=', 'department.id')
            ->join('interview', 'application.id', '=', 'interview.application_id')
            ->where('interview.interviewed', true)
            ->where('interview.interviewer_id', $interviewerId)
            ->where(function ($query) use ($departmentId, $semesterId) {
                if ($departmentId !== null) {
                    $query->where('department.id', $departmentId);
                }
                if ($semesterId !== null) {
                    $query->where('semester.id', $semesterId);
                }
            })
            ->orderBy('application.user_created', 'asc')
            ->select('application.*')
            ->get()
            ->toArray();
    }

    /**
     * Find assigned applicants.
     *
     * @param Department|null $department
     * @param Semester|null $semester
     * @return Admission[]
     */
    public function findAssignedApplicants($department = null, $semester = null): array
    {
        $query = Application::query()
            ->join('application_statistic', 'application.id', '=', 'application_statistic.application_id')
            ->join('semester', 'application_statistic.semester_id', '=', 'semester.id')
            ->join('department', 'semester.department_id', '=', 'department.id')
            ->join('interview', 'application.id', '=', 'interview.application_id')
            ->where('interview.interviewed', false);

        if ($department !== null) {
            $departmentId = $department instanceof Department ? $department->id : $department;
            $query->where('department.id', $departmentId);
        }

        if ($semester !== null) {
            $semesterId = $semester instanceof Semester ? $semester->id : $semester;
            $query->where('semester.id', $semesterId);
        }

        return $query->select('application.*')
            ->get()
            ->toArray();
    }

    /**
     * Find new applicants.
     *
     * @param Department|null $department
     * @param Semester|null $semester
     * @return Admission[]
     */
    public function findNewApplicants($department = null, $semester = null): array
    {
        $query = Application::query()
            ->join('application_statistic', 'application.id', '=', 'application_statistic.application_id')
            ->join('semester', 'application_statistic.semester_id', '=', 'semester.id')
            ->join('department', 'semester.department_id', '=', 'department.id')
            ->leftJoin('interview', 'application.id', '=', 'interview.application_id')
            ->where(function ($query) {
                $query->where('interview.interviewed', false)
                      ->orWhereNull('interview.id');
            });

        if ($department !== null) {
            $departmentId = $department instanceof Department ? $department->id : $department;
            $query->where('department.id', $departmentId);
        }

        if ($semester !== null) {
            $semesterId = $semester instanceof Semester ? $semester->id : $semester;
            $query->where('semester.id', $semesterId);
        }

        return $query->select('application.*')
            ->get()
            ->toArray();
    }

    /**
     * Find applicant by ID.
     *
     * @param int $id
     * @return Admission
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findApplicantById($id): Admission
    {
        $application = Application::find($id);
        
        if (!$application) {
            throw new ModelNotFoundException("Admission with ID {$id} not found.");
        }

        // Return as Admission (which is Application)
        return $application;
    }

    /**
     * Find applicant statistic by ID.
     *
     * @param int $id
     * @return object ApplicationStatistic entity
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findApplicantStatisticById($id): object
    {
        $statistic = ApplicationStatistic::find($id);
        
        if (!$statistic) {
            throw new ModelNotFoundException("ApplicationStatistic with ID {$id} not found.");
        }

        return $statistic;
    }

    /**
     * Get number of applications.
     *
     * @return int
     */
    public function numOfApplications(): int
    {
        return ApplicationStatistic::count();
    }

    /**
     * Get number of semesters.
     *
     * @return int
     */
    public function numOfSemesters(): int
    {
        return ApplicationStatistic::distinct('semester_id')->count('semester_id');
    }

    /**
     * Get number of applications by semester.
     *
     * @param Semester $semester
     * @return int
     */
    public function numOfSemester($semester): int
    {
        $semesterId = $semester instanceof Semester ? $semester->id : $semester;
        return ApplicationStatistic::where('semester_id', $semesterId)->count();
    }

    /**
     * Get number of applications by gender.
     *
     * @param int $gender
     * @return int
     */
    public function numOfGender($gender): int
    {
        return ApplicationStatistic::where('gender', $gender)->count();
    }

    /**
     * Get number of previous participations.
     *
     * @param bool $participated
     * @return int
     */
    public function numOfPreviousParticipation($participated): int
    {
        return ApplicationStatistic::where('previous_participation', $participated)->count();
    }

    /**
     * Get number of accepted applications.
     *
     * @param bool $accepted
     * @return int
     */
    public function numOfAccepted($accepted): int
    {
        return ApplicationStatistic::where('accepted', $accepted)->count();
    }

    /**
     * Get number of applications by year of study.
     *
     * @param int $yearOfStudy
     * @return int
     */
    public function numOfYearOfStudy($yearOfStudy): int
    {
        return ApplicationStatistic::where('year_of_study', $yearOfStudy)->count();
    }

    /**
     * Get number of applications by field of study.
     *
     * @param string $fieldOfStudy
     * @return int
     */
    public function numOfFieldOfStudy($fieldOfStudy): int
    {
        return ApplicationStatistic::where('field_of_study', $fieldOfStudy)->count();
    }

    /**
     * Get number of applications by department.
     *
     * @param int $department
     * @return int
     */
    public function numOfDepartment($department): int
    {
        $departmentId = $department instanceof Department ? $department->id : $department;
        
        return ApplicationStatistic::query()
            ->join('semester', 'application_statistic.semester_id', '=', 'semester.id')
            ->where('semester.department_id', $departmentId)
            ->count();
    }
}

