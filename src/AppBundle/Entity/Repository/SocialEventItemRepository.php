<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Department;
use \Doctrine\ORM\EntityRepository;
use \AppBundle\Entity\Semester;

/**
 * Class SocialEventItemRepository
 */
class SocialEventItemRepository extends EntityRepository
{

    /**
     * @param Semester $semester
     * @param Department $department
     * @return array
     */
    public function findSocialEventsBySemesterAndDepartment(Semester $semester, Department $department)
    {
        $socialEvents = $this->createQueryBuilder('SocialEventItem')
            ->select('SocialEventItem')
            ->where('SocialEventItem.semester = :semester or SocialEventItem.semester is null')
            ->andWhere('SocialEventItem.department = :department or SocialEventItem.department is null')
            ->orderBy('SocialEventItem.startTime')
            ->setParameter('semester', $semester)
            ->setParameter('department', $department)
            ->getQuery()
            ->getResult();
        return $socialEvents;
    }

    public function findFutureSocialEventsBySemesterAndDepartment(Semester $semester, Department $department)
    {
        $socialEvents = $this->createQueryBuilder('SocialEventItem')
            ->select('SocialEventItem')
            ->where('SocialEventItem.semester = :semester or SocialEventItem.semester is null')
            ->andWhere('SocialEventItem.department = :department or SocialEventItem.department is null')
            ->andWhere('SocialEventItem.startTime >= :now')
            ->orderBy('SocialEventItem.startTime')
            ->setParameter('semester', $semester)
            ->setParameter('department', $department)
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getResult();
        return $socialEvents;
    }
}
