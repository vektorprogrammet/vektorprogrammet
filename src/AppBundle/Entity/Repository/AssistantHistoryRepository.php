<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\AssistantHistory;
use AppBundle\Entity\Department;
use AppBundle\Entity\School;
use AppBundle\Entity\Semester;
use AppBundle\Entity\User;
use AppBundle\Utils\SemesterUtil;
use Doctrine\ORM\EntityRepository;

class AssistantHistoryRepository extends EntityRepository
{
    private function findByUserInit(User $user)
    {
        return $this->createQueryBuilder('assistantHistory')
            ->select('assistantHistory')
            ->where('assistantHistory.user = :user')
            ->setParameter('user', $user);
    }

    /**
     * @param User $user
     *
     * @return AssistantHistory[]
     */
    public function findByUser(User $user): array
    {
        return $this->findByUserInit($user)
            ->getQuery()
            ->getResult();
    }


    /**
     * @param User $user
     *
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
     * @param Department $department
     * @param Semester $semester
     *
     * @return AssistantHistory[]
     */
    public function findByDepartmentAndSemester(Department $department, Semester $semester): array
    {
        return $this->createQueryBuilder('assistantHistory')
            ->select('assistantHistory')
            ->where('assistantHistory.department = :department')
            ->andWhere('assistantHistory.semester = :semester')
            ->setParameters(array(
                'department' => $department,
                'semester' => $semester,
            ))
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $user
     *
     * @return AssistantHistory[]
     */
    public function findActiveAssistantHistoriesByUser($user): array
    {
        $today = new \DateTime('now');
        $assistantHistories = $this->getEntityManager()->createQuery('
		
		SELECT ahistory
		FROM AppBundle:AssistantHistory ahistory
		JOIN ahistory.school school
		JOIN ahistory.semester semester
		JOIN ahistory.user user 
		WHERE ahistory.user = :user
		AND semester.year = :year
	    AND semester.semesterTime = :semesterTime	
		')
            ->setParameters(array(
                'user' => $user,
                'year' => SemesterUtil::timeToYear($today),
                'semesterTime' => SemesterUtil::timeToSemesterTime($today)
            ))
            ->getResult();

        return $assistantHistories;
    }

    /**
     * @param School $school
     *
     * @return AssistantHistory[]
     */
    public function findActiveAssistantHistoriesBySchool(School $school): array
    {
        $today = new \DateTime('now');
        $assistantHistories = $this->getEntityManager()->createQuery('

		SELECT ahistory
		FROM AppBundle:AssistantHistory ahistory
		JOIN ahistory.school school
		JOIN ahistory.semester semester
		JOIN ahistory.user user
		WHERE ahistory.school = :school
		AND semester.year = :year
	    AND semester.semesterTime = :semesterTime	
		')
            ->setParameters(array(
                'school' => $school,
                'year' => SemesterUtil::timeToYear($today),
                'semesterTime' => SemesterUtil::timeToSemesterTime($today)
            ))
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
		FROM AppBundle:AssistantHistory ahistory
		JOIN ahistory.school school
		JOIN ahistory.semester semester
		JOIN ahistory.user user 
		WHERE semester.year = :year
	    AND semester.semesterTime = :semesterTime	
		')
            ->setParameters(array(
                'year' => SemesterUtil::timeToYear($today),
                'semesterTime' => SemesterUtil::timeToSemesterTime($today)
            ))
            ->getResult();

        return $assistantHistories;
    }

    /**
     * @param School $school
     *
     * @return AssistantHistory[]
     */
    public function findInactiveAssistantHistoriesBySchool(School $school): array
    {
        $today = new \DateTime('now');
        $assistantHistories = $this->getEntityManager()->createQuery('
		
		SELECT ahistory
		FROM AppBundle:AssistantHistory ahistory
		JOIN ahistory.school school
		JOIN ahistory.semester semester
		JOIN ahistory.user user 
		WHERE ahistory.school = :school
		AND NOT (
            semester.year = :year
            AND semester.semesterTime = :semesterTime
        )
		')
            ->setParameters(array(
                'school' => $school,
                'year' => SemesterUtil::timeToYear($today),
                'semesterTime' => SemesterUtil::timeToSemesterTime($today)
            ))
            ->getResult();

        return $assistantHistories;
    }

    /**
     * @param Semester $semester
     *
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
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
     * @param Semester $semester
     *
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
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
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
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
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
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
     *
     */
    public function findAllBolkNames() : array
    {
        $bolkNames = $this->createQueryBuilder('ah')
            ->select('ah.bolk')
            ->distinct()
            ->getQuery()
            ->getResult();

        $names = array();
        foreach ($bolkNames as $name) {
            $names[] = array_pop($name);
        }
        $bolkNames = array_combine($names, $names);

        return $bolkNames;
    }
}
