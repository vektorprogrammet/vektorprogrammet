<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Department;
use AppBundle\Form\Type\CreateDepartmentType;
use Symfony\Component\HttpFoundation\JsonResponse;

class DepartmentController extends Controller
{
    public function showAction()
    {
        return $this->render('department_admin/index.html.twig', array());
    }

    public function createDepartmentAction(Request $request)
    {
        $department = new Department();

        $form = $this->createForm(new CreateDepartmentType(), $department, array(
            'validation_groups' => array('create_department'),
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($department);
            $em->flush();

            return $this->redirectToRoute('departmentadmin_show');
        }

        return $this->render('department_admin/create_department.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function getAllDepartmentsForTopbarAction()
    {
        $em = $this->getDoctrine()->getManager();

        $departments = $em->getRepository('AppBundle:Department')->findActive();

        return $this->render('home/department_loop.html.twig', array(
            'departments' => $departments,
        ));
    }

    public function deleteDepartmentByIdAction(Department $department)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($department);
        $em->flush();

        return new JsonResponse(array('success' => true));
    }

    public function updateDepartmentAction(Request $request, Department $department)
    {
        $form = $this->createForm(new CreateDepartmentType(), $department);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($department);
            $em->flush();

            return $this->redirectToRoute('departmentadmin_show');
        }

        return $this->render('department_admin/create_department.html.twig', array(
            'department' => $department,
            'form' => $form->createView(),
        ));
    }
}
