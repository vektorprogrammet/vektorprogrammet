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
        if ($this->get('security.context')->isGranted('ROLE_ADMIN')) {
            // Finds all the departments
            $allDepartments = $this->getDoctrine()->getRepository('AppBundle:Department')->findAll();

            // Return the view with suitable variables
            return $this->render('department_admin/index.html.twig', array(
                'departments' => $allDepartments,
            ));
        } else {
            return $this->redirect($this->generateUrl('home'));
        }
    }

    public function createDepartmentAction(request $request)
    {

        // Only create if it is a HIGHEST_ADMIN
        if ($this->get('security.context')->isGranted('ROLE_HIGHEST_ADMIN')) {

            // Create a new Team entity
            $deparment = new Department();

            // Create a new formType with the needed variables
            $form = $this->createForm(new CreateDepartmentType(), $deparment);

            // Handle the form
            $form->handleRequest($request);

            if ($form->isValid()) {
                // Persist the team to the database
                $em = $this->getDoctrine()->getManager();
                $em->persist($deparment);
                $em->flush();

                return $this->redirect($this->generateUrl('departmentadmin_show'));
            }

            return $this->render('department_admin/create_department.html.twig', array(
                'form' => $form->createView(),
            ));
        } else {
            return $this->redirect($this->generateUrl('home'));
        }
    }

    public function getAllDepartmentsForTopbarAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        // Find all departments
        $departments = $em->getRepository('AppBundle:Department')->findAll();

        return $this->render('home/department_loop.html.twig', array(
            'departments' => $departments,
        ));
    }

    public function deleteDepartmentByIdAction(request $request)
    {
        $id = $request->get('id');

        try {
            // Only HIGHEST_ADMIN can delete departments
            if ($this->get('security.context')->isGranted('ROLE_HIGHEST_ADMIN')) {

                // This deletes the given department
                $em = $this->getDoctrine()->getEntityManager();
                $department = $this->getDoctrine()->getRepository('AppBundle:Department')->find($id);
                $em->remove($department);
                $em->flush();

                // Send a response back to AJAX
                $response['success'] = true;
            } else {
                // Send a response back to AJAX
                $response['success'] = false;
                $response['cause'] = 'Du har ikke tilstrekkelige rettigheter.';
            }
        } catch (\Exception $e) {
            // Send a response back to AJAX
            $response['success'] = false;
            $response['cause'] = 'Kunne ikke slette avdelingen.';
            //$response['cause'] = $e->getMessage(); // if you want to see the exception message.

            return new JsonResponse($response);
        }

        // Send a respons to ajax
        return new JsonResponse($response);
    }

    public function updateDepartmentAction(request $request)
    {

        // Get the ID variable from the request
        $id = $request->get('id');

        // Create a new Department entity
        $department = new Department();

        $em = $this->getDoctrine()->getManager();

        // Find a department by the ID sent in by the request
        $department = $em->getRepository('AppBundle:Department')->find($id);

        // Only edit if it is a SUPER_ADMIN
        if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {

            // Create the form
            $form = $this->createForm(new CreateDepartmentType(), $department);

            // Handle the form
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->persist($department);
                $em->flush();

                return $this->redirect($this->generateUrl('departmentadmin_show'));
            }

            return $this->render('department_admin/create_department.html.twig', array(
                'department' => $department,
                'form' => $form->createView(),
            ));
        } else {
            return $this->redirect($this->generateUrl('departmentadmin_show'));
        }
    }
}
