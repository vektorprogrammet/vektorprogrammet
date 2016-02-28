<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MembersController extends Controller {

    public function showAction($departmentId){

		// Get entity manager
		$em = $this->getDoctrine()->getEntityManager();
		$user = $this->get('security.token_storage')->getToken()->getUser();

		$days = array('Mandag','Tirsdag','Onsdag','Torsdag','Fredag','Uten dag');
		if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
			$department = (is_null($departmentId))? $user->getFieldOfStudy()->getDepartment():$em->getRepository('AppBundle:Department')->findDepartmentById($departmentId)[0];
		}else{
			$department = $user->getFieldOfStudy()->getDepartment();
		}

		$schools = $em->getRepository('AppBundle:School')->findSchoolsByDepartment($department);

		$bolks = array();
		foreach(array('Bolk 1', 'Bolk 2') as $bolk){
			$bolks[$bolk] = array();
			foreach($schools as $school){
				$bolks[$bolk][$school->getName()] = array();

				foreach($days as $d){
					$bolks[$bolk][$school->getName()][$d] = array();
				}

				$assistantHistories = $em->getRepository('AppBundle:AssistantHistory')->findActiveAssistantHistoriesBySchool($school);
				foreach($assistantHistories as $ah){
					if($ah->getBolk() === $bolk || $ah->getBolk() === 'Bolk 1, Bolk 2'){
						if($ah->getDay() !== ""){
							array_push($bolks[$bolk][$school->getName()][$ah->getDay()],$ah->getUser());
						}
						else{
							array_push($bolks[$bolk][$school->getName()]['Uten dag'],$ah->getUser());
						}
					}
				}
				foreach($days as $d){
					if(count($bolks[$bolk][$school->getName()][$d]) == 0){
						unset($bolks[$bolk][$school->getName()][$d]);
					}
				}
				if(count($bolks[$bolk][$school->getName()]) == 0){
					unset($bolks[$bolk][$school->getName()]);
				}
			}
		}

		// Find all departments
		$departments = $em->getRepository('AppBundle:Department')->findAll();

		// Return the view to be rendered
		return $this->render('members/members.html.twig', array(
			'bolks' => $bolks,
			'departments' => $departments,
			'department' => $department,
		));
    }

}
