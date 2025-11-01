<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use AppBundle\Form\Type\CreateDepartmentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class DepartmentController extends BaseController
{
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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
            $this->entityManager->persist($department);
            $this->entityManager->flush();

            $this->addFlash("success", "$department ble opprettet");

            return $this->redirectToRoute('departmentadmin_show');
        }

        return $this->render('department_admin/create_department.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function deleteDepartmentByIdAction(Department $department)
    {
        $this->entityManager->remove($department);
        $this->entityManager->flush();

        $this->addFlash("success", "Avdelingen ble slettet");

        return $this->redirectToRoute("departmentadmin_show");
    }

    public function updateDepartmentAction(Request $request, Department $department)
    {
        $form = $this->createForm(CreateDepartmentType::class, $department);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($department);
            $this->entityManager->flush();

            $this->addFlash("success", "$department ble oppdatert");

            return $this->redirectToRoute('departmentadmin_show');
        }

        return $this->render('department_admin/create_department.html.twig', array(
            'department' => $department,
            'form' => $form->createView(),
        ));
    }
}
