<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ParticipantHistoryController extends Controller
{
    public function showAction(Request $request, $id = null) {
		// Find all work histories by department
		$workHistories = $this->getDoctrine()->getRepository('AppBundle:WorkHistory')->findWorkHistoriesByDepartment($id);

		// Finds the department we're currently testing for
		$department = $this->getDoctrine()->getRepository('AppBundle:Department')->findOneBy(array('id'=>$id));

		if(is_null($department)){
			$department = $this->getUser()->getFieldOfStudy()->getDepartment();
		}
		$semesterId = $request->query->get('semester');
		$semester = null;
		if(!is_null($semesterId)){
			$semester = $this->getDoctrine()->getRepository('AppBundle:Semester')->find($semesterId);
		}

		// Find all assistantHistories by department
		$assistantHistories = $this->getDoctrine()->getRepository('AppBundle:AssistantHistory')->findAssistantHistoriesByDepartment($department, $semester);

		// Find all departments
		$departments = $this->getDoctrine()->getRepository('AppBundle:Department')->findAll();

		$semesters =  $this->getDoctrine()->getRepository('AppBundle:Semester')->findBy(array('department'=>$department), array('semesterStartDate'=>'DESC'));

		//Finds the semester to sort by
		$sortBySemester = $request->query->get('semester', null);


		return $this->render('participant_history/index.html.twig', array(
			'workHistories' => $workHistories,
			'assistantHistories' => $assistantHistories,
			'departments' => $departments,
			'department' => $department,
			'semesters' => $semesters,
			'semester' => $semester,
			'sortBySemester' => $sortBySemester,
		));
		
    }
}