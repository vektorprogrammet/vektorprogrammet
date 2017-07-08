<?php

namespace AppBundle\Controller;

use AppBundle\Entity\SchoolCapacity;
use AppBundle\Form\Type\SchoolCapacityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SchoolCapacityController extends Controller
{
    public function createAction(Request $request)
    {
	    $user = $this->getUser();
	    $department = $user->getDepartment();
	    $currentSemester = $this->getDoctrine()->getRepository('AppBundle:Semester')->findCurrentSemesterByDepartment($department);

	    $schoolCapacity = new SchoolCapacity();
	    $schoolCapacity->setSemester($currentSemester);
	    $form = $this->createForm(new SchoolCapacityType(), $schoolCapacity);
	    $form->handleRequest($request);

	    if ($form->isValid()) {
//		    return $this->render('school_admin/school_allocate_create.html.twig', array(
//			    'message' => 'Skolen eksisterer allerede',
//			    'form' => $form->createView(),
//		    ));

		    $em = $this->getDoctrine()->getManager();
		    $em->persist($schoolCapacity);
		    $em->flush();

		    return $this->redirect($this->generateUrl('school_allocation'));
	    }

	    return $this->render('school_admin/school_allocate_create.html.twig', array(
		    'message' => '',
		    'form' => $form->createView(),
	    ));
    }
}
