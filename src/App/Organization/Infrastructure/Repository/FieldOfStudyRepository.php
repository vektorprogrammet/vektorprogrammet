<?php

namespace App\Organization\Infrastructure\Repository;

use App\Organization\Infrastructure\Entity\Department;
use App\Organization\Infrastructure\Entity\FieldOfStudy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FieldOfStudyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FieldOfStudy::class);
    }

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
