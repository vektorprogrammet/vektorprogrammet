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

            return $this->redirectToRoute('semesteradmin_show');
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

    public function superadminCreateSemesterAction(Request $request)
    {
        $semester = new Semester();

        // Get the ID parameter sent in by the request
        $departmentId = $request->get('id');

        // Find the department where ID matches departmentId
        $department = $this->getDoctrine()->getRepository('AppBundle:Department')->find($departmentId);

        // Create the form
        $form = $this->createForm(new CreateSemesterType(), $semester);

        // Handle the form
        $form->handleRequest($request);

        // The fields of the form is checked if they contain the correct information
        if ($form->isValid()) {
            //Check if semester already exists
            $existingSemester = $this->getDoctrine()->getManager()->getRepository('AppBundle:Semester')->findBy(array(
                'department' => $department,
                'semesterTime' => $semester->getSemesterTime(),
                'year' => $semester->getYear(),
            ));
            //Return to semester page if semester already exists
            if (count($existingSemester)) {
                return $this->redirect($this->generateUrl('semesteradmin_show'));
            }

            // Set the department of the semester
            $semester->setDepartment($department);

            $year = $semester->getYear();
            $startMonth = $semester->getSemesterTime() == 'Vår' ? '01' : '08';
            $endMonth = $semester->getSemesterTime() == 'Vår' ? '07' : '12';
            $semester->setSemesterStartDate(date_create($year.'-'.$startMonth.'-01 00:00:00'));
            $semester->setSemesterEndDate(date_create($year.'-'.$endMonth.'-31 23:59:59'));
            // If valid insert into database
            $em = $this->getDoctrine()->getManager();
            $em->persist($semester);
            $em->flush();

            return $this->redirect($this->generateUrl('semesteradmin_show'));
        }

        // Render the view
        return $this->render('semester_admin/create_semester.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function deleteSemesterByIdAction(Request $request)
    {
        $id = $request->get('id');

        try {
            if ($this->get('security.context')->isGranted('ROLE_HIGHEST_ADMIN')) {

                // This deletes the given semester
                $em = $this->getDoctrine()->getEntityManager();
                $semester = $this->getDoctrine()->getRepository('AppBundle:Semester')->find($id);
                $em->remove($semester);
                $em->flush();

                $response['success'] = true;
            } else {
                // Send a response back to AJAX
                $response['success'] = false;
                $response['cause'] = 'Ikke tilstrekkelige rettigheter.';
            }
        } catch (\Exception $e) {
            // Send a response back to AJAX
            return new JsonResponse([
                'success' => false,
                'code' => $e->getCode(),
                'cause' => 'Det er ikke mulig å slette semesteret. Vennligst kontakt IT-ansvarlig.',
                // 'cause' => $e->getMessage(), if you want to see the exception message.
            ]);
        }

        // Send a respons to ajax
        return new JsonResponse($response);
    }
}
