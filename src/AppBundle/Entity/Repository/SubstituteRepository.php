<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Department;
use Doctrine\ORM\EntityRepository;

/**
 * SubstituteRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SubstituteRepository extends EntityRepository
{
    public function findSubstitutesByDepartment(Department $department){
        return $this->createQueryBuilder('substitute')
            ->select('substitute')
            ->join('substitute.interview', 'interview')
            ->join('interview.user', 'user')
            ->join('user.fieldOfStudy', 'fos')
            ->join('fos.department', 'd')
            ->where('d = :department')
            ->setParameter('department', $department)
            ->getQuery()
            ->getResult();
    }
}
