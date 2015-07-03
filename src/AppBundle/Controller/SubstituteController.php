<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Department;
use AppBundle\Entity\Substitute;
use AppBundle\Entity\Application;
use AppBundle\Form\Type\SubstituteType;

/**
 * SubstituteController is the controller responsible for substitute assistants,
 * such as showing and deleting substitutes.
 *
 * @package AppBundle\Controller
 */
class SubstituteController extends Controller
{
    /**
     * Shows the substitute page for the department of the logged in user.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request)
    {
        // Get query strings for filtering by semester
        $semester = $request->query->get('semester', null);

        $em = $this->getDoctrine()->getManager();

        // Finds the department for the current logged in user
        $department = $this->getUser()->getFieldOfStudy()->getDepartment();

        // Find all the substitutes for the department of the user and the given semester (can be null)
        $substitutes = $em->getRepository('AppBundle:Substitute')->findSubstitutes($department, $semester);

        $departments = $em->getRepository('AppBundle:Department')->findAllDepartments();

        // Find all the semesters associated with the department
        $semesters =  $this->getDoctrine()->getRepository('AppBundle:Semester')->findAllSemestersByDepartment($department);

        return $this->render('substitute/index.html.twig', array(
            'substitutes' => $substitutes,
            'departments' => $departments,
            'semesters' => $semesters
        ));
    }

    /**
     * Shows the substitute page for the given department.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showSubstitutesByDepartmentAction(Request $request, Department $department)
    {
        // Get query strings for filtering by semester
        $semester = $request->query->get('semester', null);

        $em = $this->getDoctrine()->getManager();

        // Find all the substitutes for the given semester (can be null) and department
        $substitutes = $em->getRepository('AppBundle:Substitute')->findSubstitutes($department, $semester);

        $departments = $em->getRepository('AppBundle:Department')->findAllDepartments();

        // Find all the semesters associated with the department
        $semesters =  $this->getDoctrine()->getRepository('AppBundle:Semester')->findAllSemestersByDepartment($department);

        return $this->render('substitute/index.html.twig', array(
            'substitutes' => $substitutes,
            'departments' => $departments,
            'semesters' => $semesters
        ));
    }

    /**
     * Creates a substitute assistant with the info from the given application.
     * If an interview has been conducted, the work day information is taken from InterviewPractical,
     * if not it is left as null values and can be filled in by manually editing the substitute.
     * This method is intended to be called by an Ajax request.
     *
     * @param Application $application
     * @return JsonResponse
     */
    public function createAction(Application $application) {
        // Only admin or team members withing the same department as the applicant can create substitute
        if($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') ||
            $application->isSameDepartment($this->getUser())) {

            $appStat = $application->getStatistic();
            $intPrac = $appStat->getInterviewPractical();

            $substitute = new Substitute();

            $substitute->setFirstName($application->getFirstName());
            $substitute->setLastName($application->getLastName());
            $substitute->setPhone($application->getPhone());
            $substitute->setEmail($application->getEmail());
            $substitute->setFieldOfStudy($appStat->getFieldOfStudy());
            $substitute->setYearOfStudy($appStat->getYearOfStudy());
            $substitute->setSemester($appStat->getSemester());

            if ($intPrac) {
                $substitute->setMonday($intPrac->getMonday());
                $substitute->setTuesday($intPrac->getTuesday());
                $substitute->setWednesday($intPrac->getWednesday());
                $substitute->setThursday($intPrac->getThursday());
                $substitute->setFriday($intPrac->getFriday());
            }

            $application->setSubstituteCreated(true);

            $em = $this->getDoctrine()->getManager();
            $em->persist($substitute);
            $em->persist($application);
            $em->flush();

            // AJAX response
            $response['success'] = true;
        } else {
            // AJAX response
            $response['success'] = false;
            $response['cause'] = 'Ikke tilstrekkelige rettigheter.';
        }

        return new JsonResponse($response);
    }

    /**
     * Shows and handles submissions of the edit substitute form.
     *
     * @param Request $request
     * @param Substitute $substitute
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Substitute $substitute) {

        $form = $this->createForm(new SubstituteType(), $substitute);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($substitute);
            $em->flush();

            return $this->redirect($this->generateUrl('substitute_show'));
        }

        return $this->render('substitute/edit.html.twig', array('form' => $form->createView()));
    }

    /**
     * Deletes the given substitute.
     * This method is intended to be called by an Ajax request.
     *
     * @param $id
     * @return JsonResponse
     */
    public function deleteAction($id){
        try {
            if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {

                // This deletes the given substitute
                $em = $this->getDoctrine()->getEntityManager();
                // Find the substitute by ID
                $substitute = $this->getDoctrine()->getRepository('AppBundle:Substitute')->find($id);
                $em->remove($substitute);
                $em->flush();

                // AJAX response
                $response['success'] = true;
            }
            else {
                // Send a response to AJAX
                $response['success'] = false;
                $response['cause'] = 'Ikke tilstrekkelige rettigheter.';
            }
        }
        catch (\Exception $e) {
            // Send a response to AJAX
            return new JsonResponse([
                'success' => false,
                'code'    => $e->getCode(),
                'cause' => 'En exception oppstod. Vennligst kontakt IT-ansvarlig.',
                // 'cause' => $e->getMessage(), if you want to see the exception message.
            ]);
        }

        // Response to ajax
        return new JsonResponse($response);
    }
}