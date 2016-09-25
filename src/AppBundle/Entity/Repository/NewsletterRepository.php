<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Department;
use AppBundle\Entity\Newsletter;
use Doctrine\ORM\EntityRepository;

class NewsletterRepository extends EntityRepository
{
    /**
     * @param Department $department
     * @return Newsletter
     */
    public function findCheckedByDepartment(Department $department)
    {
        return $this->createQueryBuilder('newsletter')
            ->select('newsletter')
            ->where('newsletter.showOnAdmissionPage = true')
            ->andWhere('newsletter.department = :department')
            ->setParameter('department', $department)
            ->getQuery()
            ->getOneOrNullResult();
    }

}