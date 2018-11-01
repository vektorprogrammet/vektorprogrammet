<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
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

    public function findEmailsBySemesterAndInfoMeeting(Semester $semester)
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
