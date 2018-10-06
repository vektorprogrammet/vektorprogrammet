<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use AppBundle\Entity\Team;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;

/**
 * TeamRepository.
 */
class TeamRepository extends EntityRepository
{

    /**
     * @param Department $department
     *
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
     * @param Department $department
     *
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
     * @param Department $department
     *
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
     * @param Department $department
     *
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
            ->setParameters(array(
                'admissionPeriod' => $admissionPeriod,
                'semester' => $admissionPeriod->getSemester(),
                'department' => $admissionPeriod->getDepartment(),
            ))
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
