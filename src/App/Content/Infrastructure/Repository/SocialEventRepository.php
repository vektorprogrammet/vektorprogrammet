<?php

namespace App\Content\Infrastructure\Repository;

use App\Entity\Department;
use App\Shared\Entity\Semester;
use App\Content\Infrastructure\Entity\SocialEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class SocialEventRepository.
 */
class SocialEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SocialEvent::class);
    }

    /**
     * @return array
     */
    public function findSocialEventsBySemesterAndDepartment(Semester $semester, Department $department)
    {
        return $this->createQueryBuilder('SocialEventItem')
            ->select('SocialEventItem')
            ->where('SocialEventItem.semester = :semester or SocialEventItem.semester is null')
            ->andWhere('SocialEventItem.department = :department or SocialEventItem.department is null')
            ->orderBy('SocialEventItem.startTime')
            ->setParameters(['semester' => $semester, 'department' => $department])
            ->getQuery()
            ->getResult();
    }

    public function findFutureSocialEventsBySemesterAndDepartment(Semester $semester, Department $department)
    {
        return $this->createQueryBuilder('SocialEventItem')
            ->select('SocialEventItem')
            ->where('SocialEventItem.semester = :semester or SocialEventItem.semester is null')
            ->andWhere('SocialEventItem.department = :department or SocialEventItem.department is null')
            ->andWhere('SocialEventItem.startTime >= :now')
            ->orderBy('SocialEventItem.startTime')
            ->setParameters(['semester' => $semester, 'department' => $department, 'now' => new \DateTime()])
            ->getQuery()
            ->getResult();
    }
}
