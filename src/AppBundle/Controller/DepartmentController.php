<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Department;
use AppBundle\Form\Type\CreateDepartmentType;

class DepartmentController extends BaseController
{
    public function showAction()
    {
        return $this->render('department_admin/index.html.twig', array());
    }

    public function createDepartmentAction(Request $request)
    {
        $department = new Department();

        $form = $this->createForm(CreateDepartmentType::class, $department);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($department);
            $em->flush();

            $this->addFlash("success", "$department ble opprettet");

            return $this->redirectToRoute('departmentadmin_show');
        }

        return $this->render('department_admin/create_department.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function deleteDepartmentByIdAction(Department $department)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($department);
        $em->flush();

        $this->addFlash("success", "Avdelingen ble slettet");

        return $this->redirectToRoute("departmentadmin_show");
    }

    public function updateDepartmentAction(Request $request, Department $department)
    {
        $form = $this->createForm(CreateDepartmentType::class, $department);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($department);
            $em->flush();

            $this->addFlash("success", "$department ble oppdatert");

            return $this->redirectToRoute('departmentadmin_show');
        }

        return $this->render('department_admin/create_department.html.twig', array(
            'department' => $department,
            'form' => $form->createView(),
        ));
    }
}
