<?php

namespace App\Repository\Eloquent;

use App\Models\AdmissionNotification;
use App\Models\Department;
use App\Models\Semester;
use App\Repository\Contract\AdmissionNotificationRepositoryInterface;

/**
 * Eloquent implementation of AdmissionNotificationRepositoryInterface.
 */
class AdmissionNotificationRepository implements AdmissionNotificationRepositoryInterface
{
    /**
     * Find emails by semester and department.
     *
     * @param Semester $semester
     * @param Department $department
     * @return string[]
     */
    public function findEmailsBySemesterAndDepartment(Semester $semester, Department $department): array
    {
        return AdmissionNotification::query()
            ->where('semester_id', $semester->id)
            ->where('department_id', $department->id)
            ->join('admission_subscriber', 'admission_notification.subscriber_id', '=', 'admission_subscriber.id')
            ->pluck('admission_subscriber.email')
            ->toArray();
    }

    /**
     * Find emails by semester and department for info meeting.
     *
     * @param Semester $semester
     * @param Department $department
     * @return string[]
     */
    public function findEmailsBySemesterAndDepartmentAndInfoMeeting(Semester $semester, Department $department): array
    {
        return AdmissionNotification::query()
            ->where('semester_id', $semester->id)
            ->where('info_meeting', true)
            ->join('admission_subscriber', 'admission_notification.subscriber_id', '=', 'admission_subscriber.id')
            ->pluck('admission_subscriber.email')
            ->toArray();
    }
}

