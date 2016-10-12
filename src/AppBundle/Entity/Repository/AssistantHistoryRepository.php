<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Semester;
use Doctrine\ORM\EntityRepository;

class AssistantHistoryRepository extends EntityRepository
{
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

    public function findActiveAssistantHistoriesBySchool($school)
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
     * @param int      $department
     * @param Semester $semester
     *
     * @return array
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
    public function numFemale(Semester $semester)
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
    public function numMale(Semester $semester)
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
}
