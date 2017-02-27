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
        // Create a new Team entity
        $department = new Department();

        // Create a new formType with the needed variables
        $form = $this->createForm(new CreateDepartmentType(), $department);

        // Handle the form
        $form->handleRequest($request);

        if ($form->isValid()) {
            // Persist the team to the database
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

        // Find all departments
        $departments = $em->getRepository('AppBundle:Department')->findAll();

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
        // Create the form
        $form = $this->createForm(new CreateDepartmentType(), $department);

        // Handle the form
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
