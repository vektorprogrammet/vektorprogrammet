<?php

namespace App\Admission\Infrastructure\Repository;

use App\Admission\Infrastructure\Entity\AdmissionNotification;
use App\Organization\Infrastructure\Entity\Department;
use App\Shared\Entity\Semester;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AdmissionNotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdmissionNotification::class);
    }

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

        return array_map(fn ($row) => $row['email'], $res);
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

        return array_map(fn ($row) => $row['email'], $res);
    }
}
