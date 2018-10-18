<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Department;
use AppBundle\Entity\FieldOfStudy;
use Doctrine\ORM\EntityRepository;

class FieldOfStudyRepository extends EntityRepository
{
    /*	Perhaps not needed anymore?

    public function findFieldOfStudyByName($shortName){
        $stmt = $this
                    ->getConnection()
                   ->prepare('
                    SELECT *
                    FROM Field_of_study F
                    WHERE shortName = :shortName
                    ');

        $stmt->bindValue('shortName', $shortName);
        $stmt->execute();

        return $stmt->fetchAll();
    }
<<<<<<< Updated upstream
    */

    public function findAllFieldOfStudy()
    {
        return $this->createQueryBuilder('FieldOfStudy')
            ->select('FieldOfStudy')
            ->distinct()
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Department $department
     *
     * @return FieldOfStudy[]
     */
    public function findByDepartment(Department $department)
    {
        return $this->createQueryBuilder('fieldOfStudy')
            ->select('fieldOfStudy')
            ->where('fieldOfStudy.department = :department')
            ->setParameter('department', $department)
            ->getQuery()
            ->getResult();
    }
}
