<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\AssistantHistory;
use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class AssistantHistoryRepository extends EntityRepository
{
    /**
     * @param User $user
     *
     * @return AssistantHistory[]
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('assistantHistory')
            ->select('assistantHistory')
            ->where('assistantHistory.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Semester $semester
     *
     * @return AssistantHistory[]
     */
    public function findBySemester(Semester $semester): array
    {
        return $this->createQueryBuilder('assistantHistory')
            ->select('assistantHistory')
            ->where('assistantHistory.semester = :semester')
            ->setParameter('semester', $semester)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $user
     *
     * @return AssistantHistory[]
     */
    public function findActiveAssistantHistoriesByUser($user)
    {
        $today = new \DateTime('now');
        $assistantHistories = $this->getEntityManager()->createQuery('
		
		SELECT ahistory
		FROM AppBundle:AssistantHistory ahistory
		JOIN ahistory.school school
		JOIN ahistory.semester semester
		JOIN ahistory.user user 
		WHERE ahistory.user = :user
		AND semester.semesterStartDate < :today
		AND semester.semesterEndDate > :today
		')
            ->setParameter('user', $user)
            ->setParameter('today', $today)
            ->getResult();

        return $assistantHistories;
    }

    /**
     * @param $school
     *
     * @return AssistantHistory[]
     */
    public function findActiveAssistantHistoriesBySchool($school): array
    {
        $today = new \DateTime('now');
        $assistantHistories = $this->getEntityManager()->createQuery('

		SELECT ahistory
		FROM AppBundle:AssistantHistory ahistory
		JOIN ahistory.school school
		JOIN ahistory.semester semester
		JOIN ahistory.user user
		WHERE ahistory.school = :school
		AND (semester.semesterStartDate < :today
		AND semester.semesterEndDate > :today)
		')
            ->setParameter('school', $school)
            ->setParameter('today', $today)
            ->getResult();

        return $assistantHistories;
    }

    public function findAllActiveAssistantHistories()
    {
        $today = new \DateTime('now');
        $assistantHistories = $this->getEntityManager()->createQuery('
		
		SELECT ahistory
		FROM AppBundle:AssistantHistory ahistory
		JOIN ahistory.school school
		JOIN ahistory.semester semester
		JOIN ahistory.user user 
		WHERE semester.semesterStartDate < :today
		AND semester.semesterEndDate > :today
		')
            ->setParameter('today', $today)
            ->getResult();

        return $assistantHistories;
    }

    public function findInactiveAssistantHistoriesBySchool($school)
    {
        $today = new \DateTime('now');
        $assistantHistories = $this->getEntityManager()->createQuery('
		
		SELECT ahistory
		FROM AppBundle:AssistantHistory ahistory
		JOIN ahistory.school school
		JOIN ahistory.semester semester
		JOIN ahistory.user user 
		WHERE ahistory.school = :school
		AND (semester.semesterStartDate > :today
		OR semester.semesterEndDate < :today)
		')
            ->setParameter('school', $school)
            ->setParameter('today', $today)
            ->getResult();

        return $assistantHistories;
    }

    /**
     * @param Department $department
     * @param Semester   $semester
     *
     * @return AssistantHistory[]
     */
    public function findAssistantHistoriesByDepartment($department, $semester = null)
    {
        $qb = $this->createQueryBuilder('AssistantHistory')
            ->select('AssistantHistory')
            ->join('AssistantHistory.semester', 's')
            ->join('s.department', 'd')
            ->where('d = ?1');

        if (!is_null($semester)) {
            $qb->andWhere('s = ?2')
                ->setParameter(2, $semester);
        }

        $qb
            ->setParameter(1, $department)
            ->orderBy('s.semesterStartDate', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Semester $semester
     *
     * @return int
     */
    public function numFemaleBySemester(Semester $semester)
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
     */
    public function numMaleBySemester(Semester $semester)
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

    public function numFemale()
    {
        return $this->createQueryBuilder('ah')
            ->select('count(ah.id)')
            ->join('ah.user', 'user')
            ->where('user.gender = 1')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function numMale()
    {
        return $this->createQueryBuilder('ah')
            ->select('count(ah.id)')
            ->join('ah.user', 'user')
            ->where('user.gender = 0')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
