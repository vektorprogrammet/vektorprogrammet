<?php

namespace AppBundle\Controller;

use AppBundle\Entity\SchoolCapacity;
use AppBundle\Form\Type\SchoolCapacityEditType;
use AppBundle\Form\Type\SchoolCapacityType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SchoolCapacityController extends BaseController
{
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse|Response
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function createAction(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $currentSemester = $this->getSemesterOrThrow404($request);

        $schoolCapacity = new SchoolCapacity();
        $schoolCapacity->setSemester($currentSemester);
        $schoolCapacity->setDepartment($department);
        $form = $this->createForm(SchoolCapacityType::class, $schoolCapacity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($schoolCapacity);
            $this->entityManager->flush();

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

        if ($form->isSubmitted() && $form->isValid()) {
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
