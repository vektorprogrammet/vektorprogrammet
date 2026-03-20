<?php

namespace App\Entity\Repository;

use App\Admission\Infrastructure\Entity\AdmissionPeriod;
use App\Entity\Department;
use App\Entity\Team;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * TeamRepository.
 */
class TeamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Team::class);
    }

    /**
     * @return QueryBuilder
     */
    private function findByDepartmentQueryBuilder(Department $department)
    {
        return $this->createQueryBuilder('team')
            ->select('team')
            ->where('team.department = :department')
            ->setParameter('department', $department);
    }

    /**
     * @return Team[]
     */
    public function findByDepartment(Department $department): array
    {
        return $this->findByDepartmentQueryBuilder($department)
            ->orderBy('team.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Team[]
     */
    public function findActiveByDepartment(Department $department): array
    {
        return $this->findByDepartmentQueryBuilder($department)
            ->andWhere('team.active = true')
            ->orderBy('team.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Team[]
     */
    public function findInActiveByDepartment(Department $department): array
    {
        return $this->findByDepartmentQueryBuilder($department)
            ->andWhere('team.active = false')
            ->orderBy('team.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Team[]
     */
    public function findByOpenApplicationAndDepartment(Department $department): array
    {
        return $this->createQueryBuilder('team')
            ->select('team')
            ->where('team.department = :department')
            ->andWhere('team.acceptApplication = true')
            ->setParameter('department', $department)
            ->getQuery()
            ->getResult();
    }

    public function findAllEmails()
    {
        $result = $this->createQueryBuilder('team')
            ->select('team.email')
            ->getQuery()
            ->getScalarResult();

        return array_column($result, 'email');
    }

    public function findByTeamInterestAndAdmissionPeriod(AdmissionPeriod $admissionPeriod)
    {
        return $this->createQueryBuilder('team')
            ->select('team')
            ->leftJoin('team.potentialMembers', 'application', Expr\Join::WITH, 'application.admissionPeriod = :admissionPeriod')
            ->leftJoin('team.potentialApplicants', 'potentialApplicant', Expr\Join::WITH, 'potentialApplicant.semester = :semester')
            ->where('application IS NOT NULL AND application.admissionPeriod = :admissionPeriod')
            ->orWhere('potentialApplicant IS NOT NULL AND potentialApplicant.department = :department')
            ->setParameters([
                'admissionPeriod' => $admissionPeriod,
                'semester' => $admissionPeriod->getSemester(),
                'department' => $admissionPeriod->getDepartment(),
            ])
            ->getQuery()
            ->getResult();
    }

    public function findByCityAndName(string $departmentCity, string $name)
    {
        return $this->createQueryBuilder('team')
            ->select('team')
            ->join('team.department', 'department')
            ->where('lower(department.city) = lower(:departmentCity)')
            ->andWhere('lower(team.name) = lower(:name)')
            ->setParameter('departmentCity', $departmentCity)
            ->setParameter('name', $name)
            ->getQuery()
            ->getResult();
    }
}
