<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use AppBundle\Entity\SocialEvent;
use AppBundle\Repository\Contract\SocialEventRepositoryInterface;
use DateTime;
use Doctrine\ORM\EntityRepository;

/**
 * Class SocialEventRepository
 */
class SocialEventRepository extends EntityRepository implements SocialEventRepositoryInterface
{
    /**
     * @param Semester $semester
     * @param Department $department
     * @return SocialEvent[]
     */
    public function findSocialEventsBySemesterAndDepartment(Semester $semester, Department $department): array
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

    /**
     * @param Semester $semester
     * @param Department $department
     * @return SocialEvent[]
     */
    public function findFutureSocialEventsBySemesterAndDepartment(Semester $semester, Department $department): array
    {
        return $this->createQueryBuilder('SocialEventItem')
            ->select('SocialEventItem')
            ->where('SocialEventItem.semester = :semester or SocialEventItem.semester is null')
            ->andWhere('SocialEventItem.department = :department or SocialEventItem.department is null')
            ->andWhere('SocialEventItem.startTime >= :now')
            ->orderBy('SocialEventItem.startTime')
            ->setParameters(['semester' => $semester, 'department' => $department, 'now' => new DateTime()])
            ->getQuery()
            ->getResult();
    }
}
