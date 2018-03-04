<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Application;
use AppBundle\Entity\Department;
use AppBundle\Event\ApplicationCreatedEvent;
use AppBundle\Form\Type\ApplicationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AssistantController extends Controller
{
    public function indexAction(Request $request, Department $department = null)
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

        // Find the relevant newsletter, based on what department the user is applying for
        $newsletter = $em->getRepository('AppBundle:Newsletter')->findCheckedByDepartment($department);

        // If the department has no active newsletter, the checkbox is removed.
        if ($newsletter === null) {
            $form->remove('wantsNewsletter');
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
        ));
    }

    public function admissionAction(Request $request, Department $department) {
      return $this->redirect($this->generateUrl('assistants', ['id' => $department->getId()]) . "#application");
    }
}
