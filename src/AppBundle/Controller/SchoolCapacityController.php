<?php

namespace AppBundle\Controller;

use AppBundle\Entity\SchoolCapacity;
use AppBundle\Form\Type\SchoolCapacityEditType;
use AppBundle\Form\Type\SchoolCapacityType;
use Symfony\Component\HttpFoundation\Request;

class SchoolCapacityController extends BaseController
{

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function createAction(Request $request)
    {
        $department = $this->getDepartmentOrThrow404();
        $currentSemester = $this->getSemesterOrThrow404();

        $schoolCapacity = new SchoolCapacity();
        $schoolCapacity->setSemester($currentSemester);
        $schoolCapacity->setDepartment($department);
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
