<?php

namespace App\Scheduling\Controller;

use App\Support\Controller\BaseController;
use App\Organization\Infrastructure\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Scheduling\Infrastructure\Entity\SchoolCapacity;
use App\Scheduling\Form\SchoolCapacityEditType;
use App\Scheduling\Form\SchoolCapacityType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SchoolCapacityController extends BaseController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    /**
     * @return RedirectResponse|Response
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    #[Route('/kontrollpanel/skole/capacity/', name: 'school_capacity_create', methods: ['GET', 'POST'])]
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
            $this->em->persist($schoolCapacity);
            $this->em->flush();

            return $this->redirect($this->generateUrl('school_allocation'));
        }

        return $this->render('school_admin/school_allocate_create.html.twig', [
            'message' => '',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/kontrollpanel/skole/capacity/{id}', name: 'school_capacity_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function editAction(Request $request, SchoolCapacity $capacity)
    {
        $form = $this->createForm(SchoolCapacityEditType::class, $capacity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($capacity);
            $this->em->flush();

            return $this->redirect($this->generateUrl('school_allocation'));
        }

        return $this->render('school_admin/school_allocate_edit.html.twig', [
            'capacity' => $capacity,
            'form' => $form->createView(),
        ]);
    }
}
