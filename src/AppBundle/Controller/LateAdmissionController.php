<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use \DateTime;
use AppBundle\Form\Type\ApplicationType;
use AppBundle\Entity\Application;
use AppBundle\Entity\ApplicationStatistics;
use AppBundle\Form\Type\ContactType;
use AppBundle\Entity\Contact;
use AppBundle\Entity\Department;

class LateAdmissionController extends Controller {		// formely AdmissionController

    public function showAction(Request $request) {

		$department = $this->get('security.token_storage')->getToken()->getUser()->getFieldOfStudy()->getDepartment();
		$id = $request->get('id');
		$em = $this->getDoctrine()->getManager();
		//$department = $em->getRepository('AppBundle:Department')->findDepartmentById($depid);
		$semesters = $em->getRepository('AppBundle:Semester')->findAllSemestersByDepartment($id);

		$today = new DateTime("now");
		foreach($semesters as $s){
			//$now = new \DateTime;
			if($s->getSemesterStartDate() < $now && $s->getSemesterEndDate() > $now){
				$semester = $s->getId();
				break;
                }
            }

		$validSemesters = array();
		// Finds the latest Semester in which the AdmissionEndDate is through. 
		foreach ($semesters as $sem) {
			if ($sem->getAdmissionEndDate() < $today && $today < $sem->getSemesterEndDate() ) {
				$validSemesters[] = $sem;
			}
		}
		
		if ( !empty($validSemesters) ) {
			
			$application = new Application();

//			$authenticated = false; // Er dette nodvendig, da den som sender inn soknadsskjema garantert er innlogget?
//			
//			// The Captcha should not appear if a user is authenticated, as said in the requirements 
//			if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
//				$authenticated = true;
//			}
			
//			$form = $this->createForm(new ApplicationType($id, $today, $authenticated), $application);
			$form = $this->createForm(new ApplicationType($id, $today, true), $application);
			
			$form->handleRequest($request);

			if ($form->isValid()) {
				$application->setSubstituteCreated(0);
				$application->setUserCreated(0);
				$application->getStatistic()->setAccepted(0);
				$em->persist($application);
				$em->flush();
				$request->getSession()->getFlashBag()->add('admission-notice', 'Søknaden er registrert.');
				return $this->redirect($this->generateUrl('admission_show_specific_department', array('id' => $id, ))); }

			return $this->render('late_admission/index.html.twig', array(
				'department' => $department,
				'semesters' => $validSemesters,
				'form' => $form->createView(),
			));
		}
		else {
			return $this->render('late_admission/index.html.twig', array(
				'department' => $department,
			));
		}
    }
}
