<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use AppBundle\Entity\Team;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;

/**
 * TeamRepository.
 */
class TeamRepository extends EntityRepository
{
    /**
     * @param Department $department
     *
     * @return Team[]
     */
    public function findByDepartment(Department $department): array
    {
        return $this->createQueryBuilder('team')
            ->select('team')
            ->where('team.department = :department')
            ->setParameter('department', $department)
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

    public function findByTeamInterestAndSemester(Semester $semester)
    {
        return $this->createQueryBuilder('team')
            ->select('team')
            ->leftJoin('team.potentialMembers', 'application', Expr\Join::WITH, 'application.semester = :semester')
            ->leftJoin('team.potentialApplicants', 'potentialApplicant', Expr\Join::WITH, 'potentialApplicant.semester = :semester')
            ->setParameter('semester', $semester)
            ->where('application IS NOT NULL')
            ->orWhere('potentialApplicant IS NOT NULL')
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
