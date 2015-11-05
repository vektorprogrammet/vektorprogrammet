<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ParticipantHistoryController extends Controller
{
    public function showAction(Request $request) {
		
		// Only Team members or above can see this page
		if ($this->get('security.context')->isGranted('ROLE_ADMIN')) {
		
			// Find all work histories
			$workHistories = $this->getDoctrine()->getRepository('AppBundle:WorkHistory')->findAll();
			
			// Find all assistantHistories
			$assistantHistories = $this->getDoctrine()->getRepository('AppBundle:AssistantHistory')->findAll();

			$activeHistories = array();

			foreach($assistantHistories as $history){
				$semester = $history->getSemester();
				$now = new \DateTime();
				if($semester->getSemesterStartDate()<$now && $semester->getSemesterEndDate() > $now){
					$activeHistories[] = $history;
				}
			}

			//$numActiveAssistants = $this->getDoctrine()->getRepository('AppBundle:AssistantHistory')->findAllActiveAssistants();

			
			// Find all departments
			$departments = $this->getDoctrine()->getRepository('AppBundle:Department')->findAll();

			// Finds the department for the current logged in user
			$department= $this->get('security.token_storage')->getToken()->getUser()->getFieldOfStudy()->getDepartment();

			$semesters =  $this->getDoctrine()->getRepository('AppBundle:Semester')->findAllSemestersByDepartment($department);

			//Finds the semester to sort by
			$sortBySemester = $request->query->get('semester', null);

			
			return $this->render('participant_history/index.html.twig', array(
				'activeHistories' => $activeHistories,
				'workHistories' => $workHistories,
				'assistantHistories' => $assistantHistories,
				'departments' => $departments,
				'department' => $department,
				'semesters' => $semesters,
				'sortBySemester' => $sortBySemester,
			));
		}
		else {
			return $this->redirect($this->generateUrl('home'));
		}
		
    }

	public function showHistoriesByDepartmentAction(Request $request, $id) {

		// Only Team members or above can see this page
		if ($this->get('security.context')->isGranted('ROLE_ADMIN')) {

			// Find all work histories by department
			$workHistories = $this->getDoctrine()->getRepository('AppBundle:WorkHistory')->findWorkHistoriesByDepartment($id);
			// Find all assistantHistories by department
			$assistantHistories = $this->getDoctrine()->getRepository('AppBundle:AssistantHistory')->findAssistantHistoriesByDepartment($id);

			// Find all departments
			$departments = $this->getDoctrine()->getRepository('AppBundle:Department')->findAll();

			// Finds the department we're currently testing for
			$department = $this->getDoctrine()->getRepository('AppBundle:Department')->findDepartmentById($id)[0];

			$semesters =  $this->getDoctrine()->getRepository('AppBundle:Semester')->findAllSemestersByDepartment($id);

			//Finds the semester to sort by
			$sortBySemester = $request->query->get('semester', null);

			$activeHistories = array();

			foreach($assistantHistories as $history){
				$semester = $history->getSemester();
				$now = new \DateTime();
				if($semester->getSemesterStartDate()<$now && $semester->getSemesterEndDate() > $now){
					$activeHistories[] = $history;
				}
			}


			return $this->render('participant_history/index.html.twig', array(
				'activeHistories' => $activeHistories,
				'workHistories' => $workHistories,
				'assistantHistories' => $assistantHistories,
				'departments' => $departments,
				'department' => $department,
				'semesters' => $semesters,
				'sortBySemester' => $sortBySemester,
			));
		}
		else {
			return $this->redirect($this->generateUrl('home'));
		}

	}
}