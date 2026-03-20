<?php

namespace App\Entity\Repository;

use App\Entity\AssistantHistory;
use App\Entity\Department;
use App\Entity\School;
use App\Shared\Entity\Semester;
use App\Entity\User;
use App\Shared\SemesterUtil;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

class AssistantHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AssistantHistory::class);
    }

    private function findByUserInit(User $user)
    {
        return $this->createQueryBuilder('assistantHistory')
            ->select('assistantHistory')
            ->where('assistantHistory.user = :user')
            ->setParameter('user', $user);
    }

    /**
     * @return AssistantHistory[]
     */
    public function findByUser(User $user): array
    {
        return $this->findByUserInit($user)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return AssistantHistory[]
     */
    public function findMostRecentByUser(User $user): array
    {
        return $this
            ->findByUserInit($user)
            ->join('assistantHistory.semester', 'sm')
            ->addOrderBy('sm.year', 'DESC')
            ->addOrderBy('sm.semesterTime', 'ASC') // Vår < Høst
            ->getQuery()
            ->getResult();
    }

    /**
     * @return AssistantHistory[]
     */
    public function findByDepartmentAndSemester(Department $department, Semester $semester): array
    {
        return $this->createQueryBuilder('assistantHistory')
            ->select('assistantHistory')
            ->where('assistantHistory.department = :department')
            ->andWhere('assistantHistory.semester = :semester')
            ->setParameters([
                'department' => $department,
                'semester' => $semester,
            ])
            ->getQuery()
            ->getResult();
    }

    /**
     * @return AssistantHistory[]
     */
    public function findActiveAssistantHistoriesByUser($user): array
    {
        $today = new \DateTime('now');
        $assistantHistories = $this->getEntityManager()->createQuery('
		
		SELECT ahistory
		FROM App\Entity\AssistantHistory ahistory
		JOIN ahistory.school school
		JOIN ahistory.semester semester
		JOIN ahistory.user user 
		WHERE ahistory.user = :user
		AND semester.year = :year
	    AND semester.semesterTime = :semesterTime	
		')
            ->setParameters([
                'user' => $user,
                'year' => SemesterUtil::timeToYear($today),
                'semesterTime' => SemesterUtil::timeToSemesterTime($today),
            ])
            ->getResult();

        return $assistantHistories;
    }

    /**
     * @return AssistantHistory[]
     */
    public function findActiveAssistantHistoriesBySchool(School $school): array
    {
        $today = new \DateTime('now');
        $assistantHistories = $this->getEntityManager()->createQuery('

		SELECT ahistory
		FROM App\Entity\AssistantHistory ahistory
		JOIN ahistory.school school
		JOIN ahistory.semester semester
		JOIN ahistory.user user
		WHERE ahistory.school = :school
		AND semester.year = :year
	    AND semester.semesterTime = :semesterTime	
		')
            ->setParameters([
                'school' => $school,
                'year' => SemesterUtil::timeToYear($today),
                'semesterTime' => SemesterUtil::timeToSemesterTime($today),
            ])
            ->getResult();

        return $assistantHistories;
    }

    /**
     * @return AssistantHistory[]
     */
    public function findAllActiveAssistantHistories(): array
    {
        $today = new \DateTime('now');
        $assistantHistories = $this->getEntityManager()->createQuery('
		
		SELECT ahistory
		FROM App\Entity\AssistantHistory ahistory
		JOIN ahistory.school school
		JOIN ahistory.semester semester
		JOIN ahistory.user user 
		WHERE semester.year = :year
	    AND semester.semesterTime = :semesterTime	
		')
            ->setParameters([
                'year' => SemesterUtil::timeToYear($today),
                'semesterTime' => SemesterUtil::timeToSemesterTime($today),
            ])
            ->getResult();

        return $assistantHistories;
    }

    /**
     * @return AssistantHistory[]
     */
    public function findInactiveAssistantHistoriesBySchool(School $school): array
    {
        $today = new \DateTime('now');
        $assistantHistories = $this->getEntityManager()->createQuery('
		
		SELECT ahistory
		FROM App\Entity\AssistantHistory ahistory
		JOIN ahistory.school school
		JOIN ahistory.semester semester
		JOIN ahistory.user user 
		WHERE ahistory.school = :school
		AND NOT (
            semester.year = :year
            AND semester.semesterTime = :semesterTime
        )
		')
            ->setParameters([
                'school' => $school,
                'year' => SemesterUtil::timeToYear($today),
                'semesterTime' => SemesterUtil::timeToSemesterTime($today),
            ])
            ->getResult();

        return $assistantHistories;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function numFemaleBySemester(Semester $semester): int
    {
        return $this->createQueryBuilder('ah')
            ->select('count(ah.id)')
            ->join('ah.user', 'user')
            ->where('user.gender = 1')
            ->andWhere('ah.semester = :semester')
            ->setParameter('semester', $semester)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function numMaleBySemester(Semester $semester): int
    {
        return $this->createQueryBuilder('ah')
            ->select('count(ah.id)')
            ->join('ah.user', 'user')
            ->where('user.gender = 0')
            ->andWhere('ah.semester = :semester')
            ->setParameter('semester', $semester)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function numFemale(): int
    {
        return $this->createQueryBuilder('ah')
            ->select('count(ah.id)')
            ->join('ah.user', 'user')
            ->where('user.gender = 1')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function numMale(): int
    {
        return $this->createQueryBuilder('ah')
            ->select('count(ah.id)')
            ->join('ah.user', 'user')
            ->where('user.gender = 0')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return string[]
     */
    public function findAllBolkNames(): array
    {
        $bolkNames = $this->createQueryBuilder('ah')
            ->select('ah.bolk')
            ->distinct()
            ->getQuery()
            ->getResult();

        $names = [];
        foreach ($bolkNames as $name) {
            $names[] = array_pop($name);
        }
        $bolkNames = array_combine($names, $names);

        return $bolkNames;
    }
}
