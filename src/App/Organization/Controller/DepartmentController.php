<?php

namespace App\Organization\Controller;

use App\Support\Controller\BaseController;
use App\Organization\Infrastructure\Entity\Department;
use App\Organization\Infrastructure\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Organization\Form\CreateDepartmentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class DepartmentController extends BaseController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    #[Route('/kontrollpanel/avdelingadmin', name: 'departmentadmin_show', methods: ['GET'])]
    public function showAction()
    {
        return $this->render('department_admin/index.html.twig', []);
    }

    #[Route('/kontrollpanel/avdelingadmin/opprett', name: 'departmentadmin_create_department', methods: ['GET', 'POST'])]
    public function createDepartmentAction(Request $request)
    {
        $department = new Department();

        $form = $this->createForm(CreateDepartmentType::class, $department);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($department);
            $this->em->flush();

            $this->addFlash('success', "$department ble opprettet");

            return $this->redirectToRoute('departmentadmin_show');
        }

        return $this->render('department_admin/create_department.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/kontrollpanel/avdelingadmin/slett/{id}', name: 'departmentadmin_delete_department_by_id', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteDepartmentByIdAction(Department $department)
    {
        $this->em->remove($department);
        $this->em->flush();

        $this->addFlash('success', 'Avdelingen ble slettet');

        return $this->redirectToRoute('departmentadmin_show');
    }

    #[Route('/kontrollpanel/avdelingadmin/update/{id}', name: 'departmentadmin_update', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function updateDepartmentAction(Request $request, Department $department)
    {
        $form = $this->createForm(CreateDepartmentType::class, $department);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($department);
            $this->em->flush();

            $this->addFlash('success', "$department ble oppdatert");

            return $this->redirectToRoute('departmentadmin_show');
        }

        return $this->render('department_admin/create_department.html.twig', [
            'department' => $department,
            'form' => $form->createView(),
        ]);
    }
}
