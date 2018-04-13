<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use AppBundle\Form\Type\EditSemesterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Semester;
use AppBundle\Form\Type\CreateSemesterType;
use Symfony\Component\HttpFoundation\JsonResponse;

class SemesterController extends Controller
{
    public function updateSemesterAction(Request $request, Semester $semester)
    {
        $form = $this->createForm(new EditSemesterType(), $semester);

        // Handle the form
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($semester);
            $em->flush();

            return $this->redirectToRoute('semester_show_by_department', array('id' => $semester->getDepartment()->getId()));
        }

        return $this->render('semester_admin/edit_semester.html.twig', array(
            'form' => $form->createView(),
            'semesterName' => $semester->getName(),
        ));
    }

    public function showSemestersByDepartmentAction(Department $department)
    {
        // Finds the semesters for the given department
        $semesters = $this->getDoctrine()->getRepository('AppBundle:Semester')->findAllSemestersByDepartment($department);

        // Renders the view with the variables
        return $this->render('semester_admin/index.html.twig', array(
            'semesters' => $semesters,
            'departmentName' => $department->getShortName(),
        ));
    }

    public function showAction()
    {
        // Finds the departmentId for the current logged in user
        $department = $this->getUser()->getDepartment();

        // Finds the users for the given department
        $semesters = $this->getDoctrine()->getRepository('AppBundle:Semester')->findAllSemestersByDepartment($department);

        return $this->render('semester_admin/index.html.twig', array(
            'semesters' => $semesters,
            'departmentName' => $department->getShortName(),
        ));
    }

    public function createSemesterAction(Request $request, Department $department)
    {
        $semester = new Semester();

        // Create the form
        $form = $this->createForm(new CreateSemesterType(), $semester);

        // Handle the form
        $form->handleRequest($request);

        // The fields of the form is checked if they contain the correct information
        if ($form->isValid()) {
            //Check if semester already exists
            $existingSemester = $this->getDoctrine()->getManager()->getRepository('AppBundle:Semester')->
            findByDepartmentAndTime($department, $semester->getSemesterTime(), $semester->getYear());

            //Return to semester page if semester already exists
            if (count($existingSemester)) {
                return $this->redirectToRoute('semester_show');
            }

            $semester->setDepartment($department);
            $semester->setStartAndEndDateByTime($semester->getSemesterTime(), $semester->getYear());

            $em = $this->getDoctrine()->getManager();
            $em->persist($semester);
            $em->flush();

            return $this->redirectToRoute('semester_show_by_department', array('id' => $department->getId()));
        }

        // Render the view
        return $this->render('semester_admin/create_semester.html.twig', array(
            'department' => $department,
            'form' => $form->createView(),
        ));
    }

    public function deleteAction(Semester $semester)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($semester);
        $em->flush();

        return new JsonResponse(array('success' => true));
    }
}
