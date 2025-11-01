<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use AppBundle\Form\Type\CreateDepartmentType;
use AppBundle\Service\Contract\DepartmentManagementServiceInterface;
use Symfony\Component\HttpFoundation\Request;

class DepartmentController extends BaseController
{
    private $departmentManagementService;

    /**
     * @param DepartmentManagementServiceInterface $departmentManagementService
     */
    public function __construct(DepartmentManagementServiceInterface $departmentManagementService)
    {
        $this->departmentManagementService = $departmentManagementService;
    }
    public function showAction()
    {
        return $this->render('department_admin/index.html.twig', array());
    }

    public function createDepartmentAction(Request $request)
    {
        $department = new Department();

        $form = $this->createForm(CreateDepartmentType::class, $department);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->departmentManagementService->createDepartment($department);

            $this->addFlash("success", "$department ble opprettet");

            return $this->redirectToRoute('departmentadmin_show');
        }

        return $this->render('department_admin/create_department.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function deleteDepartmentByIdAction(Department $department)
    {
        $this->departmentManagementService->deleteDepartment($department);

        $this->addFlash("success", "Avdelingen ble slettet");

        return $this->redirectToRoute("departmentadmin_show");
    }

    public function updateDepartmentAction(Request $request, Department $department)
    {
        $form = $this->createForm(CreateDepartmentType::class, $department);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->departmentManagementService->updateDepartment($department);

            $this->addFlash("success", "$department ble oppdatert");

            return $this->redirectToRoute('departmentadmin_show');
        }

        return $this->render('department_admin/create_department.html.twig', array(
            'department' => $department,
            'form' => $form->createView(),
        ));
    }
}
