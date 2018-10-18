<?php

namespace AppBundle\Controller;

use AppBundle\Entity\SchoolCapacity;
use AppBundle\Form\Type\SchoolCapacityEditType;
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
        $form = $this->createForm(SchoolCapacityType::class, $schoolCapacity);
        $form->handleRequest($request);

        if ($form->isValid()) {
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

    public function editAction(Request $request, SchoolCapacity $capacity)
    {
        $form = $this->createForm(SchoolCapacityEditType::class, $capacity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($capacity);
            $em->flush();

            return $this->redirect($this->generateUrl('school_allocation'));
        }

        return $this->render('school_admin/school_allocate_edit.html.twig', array(
            'capacity' => $capacity,
            'form' => $form->createView(),
        ));
    }
}
