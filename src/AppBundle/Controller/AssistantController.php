<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Application;
use AppBundle\Entity\Department;
use AppBundle\Event\ApplicationCreatedEvent;
use AppBundle\Form\Type\ApplicationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AssistantController extends Controller
{
    /**
     * @Route("/opptak")
     * @Route("/opptak/avdeling/{id}", name="admission_show_specific_department", requirements={"id"="\d+"})
     * @Route("/opptak/{shortName}", name="admission_show_by_short_name", requirements={"shortName"="\w+"})
     * @Route("/avdeling/{shortName}", name="admission_show_specific_department_by_name", requirements={"shortName"="\w+"})
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param Department $department
     *
     * @return Response
     */
    public function admissionAction(Request $request, Department $department = null)
    {
        return $this->indexAction($request, $department, true);
    }

    /**
     * @param Request $request
     * @param Department $department
     * @param bool $scrollToAdmissionForm
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, Department $department = null, $scrollToAdmissionForm = false)
    {
        $admissionManager = $this->get('app.application_admission');
        $em = $this->getDoctrine()->getManager();
        $departments = $em->getRepository('AppBundle:Department')->findAll();
        if (null === $department) {
            $department = $this->get('app.geolocation')->findNearestDepartment($departments);
        }

        $semester = $em->getRepository('AppBundle:Semester')->findSemesterWithActiveAdmissionByDepartment($department);

        $teams = $em->getRepository('AppBundle:Team')->findByOpenApplicationAndDepartment($department);

        $application = new Application();

        $form = $this->createForm(ApplicationType::class, $application, array(
            'validation_groups' => array('admission'),
            'departmentId' => $department->getId(),
            'environment' => $this->get('kernel')->getEnvironment(),
        ));

        if ($form->isSubmitted()) {
            $scrollToAdmissionForm = true;
        }

        $form->handleRequest($request);

        if ($form->isValid()) {
            $admissionManager->setCorrectUser($application);

            if ($application->getUser()->hasBeenAssistant()) {
                return $this->redirectToRoute('admission_existing_user');
            }

            $application->setSemester($semester);
            $em->persist($application);
            $em->flush();

            $this->get('event_dispatcher')->dispatch(ApplicationCreatedEvent::NAME, new ApplicationCreatedEvent($application));

            return $this->redirectToRoute('application_confirmation');
        }

        return $this->render('assistant/assistants.html.twig', array(
            'department' => $department,
            'semester' => $semester,
            'teams' => $teams,
            'form' => $form->createView(),
            'scroll_to_admission_form' => $scrollToAdmissionForm,
        ));
    }

    /**
     * @Route("/assistenter/opptak/bekreftelse", name="application_confirmation")
     * @return Response
     */
    public function confirmationAction()
    {
        return $this->render('admission/application_confirmation.html.twig');
    }
}
