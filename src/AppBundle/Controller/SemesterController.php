<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\EditSemesterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Semester;
use AppBundle\Form\Type\CreateSemesterType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\DateTime;

class SemesterController extends Controller {

	public function updateSemesterAction(request $request){

		$id = $request->get('id');

		$em = $this->getDoctrine()->getManager();
		$semester = $em->getRepository('AppBundle:Semester')->find($id);

		$form = $this->createForm(new EditSemesterType(), $semester);

		// Handle the form
		$form->handleRequest($request);

		if ($form->isValid()) {
			$em->persist($semester);
			$em->flush();
			return $this->redirect($this->generateUrl('semesteradmin_show'));
		}

		return $this->render('semester_admin/edit_semester.html.twig', array(
			'form' => $form->createView(),
			'semesterName' => $semester->getName()
		));

	}
		// If it is an admin they can only edit semesters that are from their own department
		/*
		************************************************************************************************************
		***** Enabe this if you want ROLE_ADMIN to be able to edit semesters from their own department  *****
		************************************************************************************************************
		
		elseif ( ($this->get('security.context')->isGranted('ROLE_ADMIN')) && ($userDepartment == $semesterDepartment) ){
			
			$form = $this->createForm(new CreateSemesterType(), $semester);
		
			// Handle the form
			$form->handleRequest($request);
			
			if ($form->isValid()) {
				$em->persist($semester);
				$em->flush();
				return $this->redirect($this->generateUrl('semesteradmin_show'));
			}
			
			return $this->render('semester_admin/create_semester.html.twig', array(
				 'form' => $form->createView(),
			));
			
		}
		*/

	public function showSemestersByDepartmentAction(request $request){
	
		if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
			// Find the department
			$department = $request->get('id');
			
			// Finds all the departments
			$allDepartments = $this->getDoctrine()->getRepository('AppBundle:Department')->findAll();
			
			// Finds the semesters for the given department
			$semesters = $this->getDoctrine()->getRepository('AppBundle:Semester')->findAllSemestersByDepartment($department);
			
			// Renders the view with the variables
			return $this->render('semester_admin/index.html.twig', array(
				'semesters' => $semesters,
				'departments' => $allDepartments,
				'departmentName' => $this->getDoctrine()->getRepository('AppBundle:Department')->find($department)->getShortName(),
			));
		}
		else {
			return $this->redirect($this->generateUrl('home'));
		}
	}
	
    public function showAction() {
		if ($this->get('security.context')->isGranted('ROLE_ADMIN')) {
			// Finds all the departments
			$allDepartments = $this->getDoctrine()->getRepository('AppBundle:Department')->findAll();
			
			// Finds the departmentId for the current logged in user
			$department = $this->get('security.token_storage')->getToken()->getUser()->getFieldOfStudy()->getDepartment();
			
			// Finds the users for the given department
			$semesters = $this->getDoctrine()->getRepository('AppBundle:Semester')->findAllSemestersByDepartment($department);
			
			return $this->render('semester_admin/index.html.twig', array(
				'semesters' => $semesters,
				'departments' => $allDepartments,
				'departmentName' => $department->getShortName(),
			));
		}
		else {
			return $this->redirect($this->generateUrl('home'));
		}
    }

	public function SuperadminCreateSemesterAction(request $request){

		if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
			$semester = new Semester();

			// Get the ID parameter sent in by the request
			$departmentId = $request->get('id');
			
			// Find the department where ID matches departmentId
			$department = $this->getDoctrine()->getRepository('AppBundle:Department')->find($departmentId);
			
			// Create the form
			$form = $this->createForm(new CreateSemesterType(), $semester);
			
			// Handle the form
			$form->handleRequest($request);
			
			// The fields of the form is checked if they contain the correct information
			if ($form->isValid()) {
				// Set the department of the semester
				$semester->setDepartment($department);

				$year = $semester->getYear();
				$startMonth = $semester->getSemesterTime() == "Vår" ? '01' : '08';
				$endMonth = $semester->getSemesterTime() == "Vår" ? '07' : '12';
				$semester->setSemesterStartDate(date_create($year.'-'.$startMonth.'-01 00:00:00'));
				$semester->setSemesterEndDate(date_create($year.'-'.$endMonth.'-31 23:59:59'));
				// If valid insert into database
				$em = $this->getDoctrine()->getManager();
				$em->persist($semester);
				$em->flush();
				return $this->redirect($this->generateUrl('semesteradmin_show'));
			}
			
			// Render the view
			return $this->render('semester_admin/create_semester.html.twig', array(
				 'form' => $form->createView(),
			));
		}
		else {
			return $this->redirect($this->generateUrl('home'));
		}
	
	}
	
	/* This allows the ROLE_ADMIN to create semester, but it was not mentioned in the requirements that the ROLE_ADMIN should be able to create semesters.
	public function createSemesterAction(request $request){
		
		if ($this->get('security.context')->isGranted('ROLE_ADMIN')) {
			$semester = new Semester();
			
			// Get the department of the current user
			$department = $this->get('security.context')->getToken()->getUser()->getFieldOfStudy()->getDepartment();
			
			// Create the form
			$form = $this->createForm(new CreateSemesterType($department), $semester);
			
			// Handle the form
			$form->handleRequest($request);
			
			// The fields of the form is checked if they contain the correct information
			if ($form->isValid()) {
				// Set the department of the semester
				$semester->setDepartment($department);
				// If valid insert into database
				$em = $this->getDoctrine()->getManager();
				$em->persist($semester);
				$em->flush();
				return $this->redirect($this->generateUrl('semesteradmin_show'));
			}
			
			// Render the view
			return $this->render('semester_admin/create_semester.html.twig', array(
				 'form' => $form->createView(),
			));
		}
		else {
			return $this->redirect($this->generateUrl('home'));
		}
	
	}
	*/
	
	public function deleteSemesterByIdAction(request $request){
		
		$id = $request->get('id');
		
		try {
			
			if ($this->get('security.context')->isGranted('ROLE_HIGHEST_ADMIN')) {
			
				// This deletes the given semester
				$em = $this->getDoctrine()->getEntityManager();
				$semester = $this->getDoctrine()->getRepository('AppBundle:Semester')->find($id);
				$em->remove($semester);
				$em->flush();
				
				$response['success'] = true;
			}
			// You have to check for admin rights here to prevent admins from deleting smesters that are not in their department
			/*
			Enable this if you want ROLE_ADMIN to be able to delete semesters from their department 
			elseif ($this->get('security.context')->isGranted('ROLE_ADMIN')){
				
				$em = $this->getDoctrine()->getEntityManager();
				// Find a semester by a given ID 
				$semester = $this->getDoctrine()->getRepository('AppBundle:Semester')->find($id);
				// Find the department of the semester that is being deleted
				$department = $semester->getDepartment();
				
				// Is the admin from the same department as the semester he is trying to delete?
				if ($this->get('security.context')->getToken()->getUser()->getFieldOfStudy()->getDepartment() === $department){
					
					$em->remove($semester);
					$em->flush();
					// Send a response back to AJAX
					$response['success'] = true;
				
				}
				else {
					// Send a response back to AJAX
					$response['success'] = false;
					$response['cause'] = 'Du kan ikke slette et semester som ikke er fra din avdeling.';
				}
			}
			*/
			else {
				// Send a response back to AJAX
				$response['success'] = false;
				$response['cause'] = 'Ikke tilstrekkelige rettigheter.';
			}
		}
		catch (\Exception $e) {
			// Send a response back to AJAX
			return new JsonResponse([
				'success' => false,
				'code'    => $e->getCode(),
				'cause' => 'Det er ikke mulig å slette semesteret. Vennligst kontakt IT-ansvarlig.',
				// 'cause' => $e->getMessage(), if you want to see the exception message. 
			]);
		}
		
		// Send a respons to ajax 
		return new JsonResponse( $response );
		
	}
		
	
}
