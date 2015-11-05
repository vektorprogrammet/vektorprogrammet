<?php

namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class AssistantHistoryRepository extends EntityRepository {

	public function findActiveAssistantHistoriesByUser($user){
	
		$today = new \DateTime('now');
		$assistantHistories =  $this->getEntityManager()->createQuery("
		
		SELECT ahistory
		FROM AppBundle:AssistantHistory ahistory
		JOIN ahistory.school school
		JOIN ahistory.semester semester
		JOIN ahistory.user user 
		WHERE ahistory.user = :user
		AND semester.semesterStartDate < :today
		AND semester.semesterEndDate > :today
		")
		->setParameter('user', $user)
		->setParameter('today', $today)
		->getResult();

		return $assistantHistories;
	}

	public function findActiveAssistantHistoriesBySchool($school){
		
		$today = new \DateTime('now');
		$assistantHistories =  $this->getEntityManager()->createQuery("
		
		SELECT ahistory
		FROM AppBundle:AssistantHistory ahistory
		JOIN ahistory.school school
		JOIN ahistory.semester semester
		JOIN ahistory.user user 
		WHERE ahistory.school = :school
		AND semester.semesterStartDate < :today
		AND semester.semesterEndDate > :today
		")
		->setParameter('school', $school)
		->setParameter('today', $today)
		->getResult();

		return $assistantHistories;
	}
	
	public function findAllActiveAssistantHistories(){
		
		$today = new \DateTime('now');
		$assistantHistories =  $this->getEntityManager()->createQuery("
		
		SELECT ahistory
		FROM AppBundle:AssistantHistory ahistory
		JOIN ahistory.school school
		JOIN ahistory.semester semester
		JOIN ahistory.user user 
		WHERE semester.semesterStartDate < :today
		AND semester.semesterEndDate > :today
		")
		->setParameter('today', $today)
		->getResult();

		return $assistantHistories;
	}
	
	public function findInactiveAssistantHistoriesBySchool($school){
		
		$today = new \DateTime('now');
		$assistantHistories =  $this->getEntityManager()->createQuery("
		
		SELECT ahistory
		FROM AppBundle:AssistantHistory ahistory
		JOIN ahistory.school school
		JOIN ahistory.semester semester
		JOIN ahistory.user user 
		WHERE ahistory.school = :school
		AND (semester.semesterStartDate > :today
		OR semester.semesterEndDate < :today)
		")
		->setParameter('school', $school)
		->setParameter('today', $today)
		->getResult();

		return $assistantHistories;
	}
	/*
	public function findActiveAssistantHistoriesByDepartment($department){

		$today = new \DateTime('now');
		$assistantHistories =  $this->getEntityManager()->createQuery("

		SELECT ahistory
		FROM AppBundle:AssistantHistory ahistory
		JOIN ahistory.semester.department department
		JOIN ahistory.semester semester
		JOIN ahistory.user user
		WHERE ahistory.semester.department = :department
		AND semester.semesterStartDate < :today
		AND semester.semesterEndDate > :today
		")
			->setParameter('department', $department)
			->setParameter('today', $today)
			->getResult();

		return $assistantHistories;
	}
	*/

	public function findAssistantHistoriesByDepartment($department){
		$today = new \DateTime('now');
		$assistantHistories =  $this->getEntityManager()->createQuery("
		SELECT ahistory
		FROM AppBundle:AssistantHistory ahistory
		JOIN ahistory.semester semester
		WHERE semester.department = :department
		AND semester.semesterStartDate < :today

		")
			->setParameter('department', $department)
			->setParameter('today', $today)
			->getResult();
		return $assistantHistories;
	}
	
}
