<?php

namespace App\Entity\Repository;

use App\Entity\Department;
use App\Entity\Semester;
use Doctrine\ORM\EntityRepository;

class AdmissionNotificationRepository extends EntityRepository
{
    public function findEmailsBySemesterAndDepartment(Semester $semester, Department $department)
    {
        $res = $this->createQueryBuilder('notification')
            ->select('subscriber.email')
            ->join('notification.subscriber', 'subscriber')
            ->where('notification.semester = :semester')
            ->andWhere('notification.department = :department')
            ->setParameter('semester', $semester)
            ->setParameter('department', $department)
            ->getQuery()
            ->getResult();

        return array_map(function ($row) {
            return $row["email"];
        }, $res);
    }

    public function findEmailsBySemesterAndDepartmentAndInfoMeeting(Semester $semester, Department $department)
    {
        $res = $this->createQueryBuilder('notification')
            ->select('subscriber.email')
            ->join('notification.subscriber', 'subscriber')
            ->where('notification.semester = :semester')
            ->andWhere('notification.infoMeeting = true')
            ->setParameter('semester', $semester)
            ->getQuery()
            ->getResult();

        return array_map(function ($row) {
            return $row["email"];
        }, $res);
    }
}
