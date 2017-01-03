<?php

namespace AppBundle\Controller;

use AppBundle\Event\InterviewConductedEvent;
use AppBundle\Service\RoleManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Interview;
use AppBundle\Entity\Application;
use AppBundle\Form\Type\ScheduleInterviewType;
use AppBundle\Form\Type\ApplicationInterviewType;
use AppBundle\Form\Type\AssignInterviewType;

/**
 * InterviewController is the controller responsible for interview actions,
 * such as showing, assigning and conducting interviews.
 */
class InterviewController extends Controller
{
    /**
     * Shows and handles the submission of the interview form.
     * The rendered page is the page used to conduct interviews.
     *
     * @param Request     $request
     * @param Application $application
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function conductAction(Request $request, Application $application)
    {
        if ($this->getUser() === $application->getUser()) {
            return $this->render('error/control_panel_error.html.twig', array('error' => 'Du kan ikke intervjue deg selv'));
        }

        // If the interview has not yet been conducted, create up to date answer objects for all questions in schema
        $interview = $this->get('app.interview.manager')->initializeInterviewAnswers($application->getInterview());

        // Only admin and above, or the assigned interviewer should be able to conduct an interview
        if (!$this->get('app.interview.manager')->loggedInUserCanSeeInterview($interview)) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(new ApplicationInterviewType(), $application, array(
            'validation_groups' => array('interview'),
        ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $isNewInterview = !$interview->getInterviewed();

            $em = $this->getDoctrine()->getManager();
            $em->persist($interview);
            $em->flush();

            if ($isNewInterview) {
                $this->get('event_dispatcher')->dispatch(InterviewConductedEvent::NAME, new InterviewConductedEvent($application));
            }

            return $this->redirect($this->generateUrl('admissionadmin_show', array('status' => 'interviewed')));
        }

        return $this->render('interview/conduct.html.twig', array(
            'application' => $application,
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Interview $interview
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function cancelAction(Interview $interview)
    {
        $interview->setCancelled(true);
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($interview);
        $manager->flush();

        return $this->redirectToRoute('admissionadmin_show', array('status' => 'assigned'));
    }

    /**
     * Shows the given interview.
     *
     * @param Application $application
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Application $application)
    {
        if (null === $interview = $application->getInterview()) {
            return $this->redirectToRoute('admissionadmin_show');
        }

        // Only accessible for admin and above, or team members belonging to the same department as the interview
        if (!$this->get('app.interview.manager')->loggedInUserCanSeeInterview($interview) ||
            $this->getUser() == $application->getUser()
        ) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('interview/show.html.twig', array('interview' => $interview, 'application' => $application));
    }

    /**
     * Deletes the given interview.
     * This method is intended to be called by an Ajax request.
     *
     * @param Interview $interview
     *
     * @return JsonResponse
     *
     * @internal param $id
     */
    public function deleteInterviewAction(Interview $interview)
    {
        try {

            // Delete the interview
            $em = $this->getDoctrine()->getManager();
            $em->remove($interview);
            $em->flush();

            // AJAX response
            return new JsonResponse(array(
                'success' => true,
            ));
        } catch (\Exception $e) {
            // Send a response to AJAX
            return new JsonResponse([
                'success' => false,
                'code' => $e->getCode(),
                'cause' => 'En feil oppstod. Vennligst kontakt IT-ansvarlig.',
            ]);
        }
    }

    /**
     * Deletes a bulk of interviews.
     * Takes a list of application ids through a form POST request, and deletes the interviews associated with them.
     *
     * This method is intended to be called by an Ajax request.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function bulkDeleteInterviewAction(Request $request)
    {
        try {
            // Only the highest user role should be able to delete an interview
            // Get the ids from the form
            $applicationIds = $request->request->get('application')['id'];

            // Get the application objects
            $em = $this->getDoctrine()->getManager();
            $applications = $em->getRepository('AppBundle:Application')->findBy(array('id' => $applicationIds));

            // Delete the interviews
            foreach ($applications as $application) {
                $interview = $application->getInterview();
                if ($interview) {
                    $em->remove($interview);
                }
            }
            $em->flush();

            // AJAX response
            return new JsonResponse(array(
                'success' => true,
            ));
        } catch (\Exception $e) {
            // Send a response to AJAX
            return new JsonResponse([
                'success' => false,
                'cause' => 'En exception oppstod. Vennligst kontakt IT-ansvarlig.',
            ]);
        }
    }

    /**
     * Shows and handles the submission of the schedule interview form.
     * This method can also send an email to the applicant with the info from the submitted form.
     *
     * @param Request     $request
     * @param Application $application
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function scheduleAction(Request $request, Application $application)
    {
        $interview = $application->getInterview();
        // Only admin and above, or the assigned interviewer should be able to book an interview
        if (!$this->get('app.interview.manager')->loggedInUserCanSeeInterview($interview)) {
            throw $this->createAccessDeniedException();
        }

        // Set the default data for the form
        $defaultData = $this->get('app.interview.manager')->getDefaultScheduleFormData($interview);

        $form = $this->createForm(new ScheduleInterviewType(), $defaultData);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

            // Update the scheduled time for the interview
            $interview->setScheduled($data['datetime']);
            $em = $this->getDoctrine()->getManager();
            $em->persist($interview);
            $em->flush();

            // Send email if the send button was clicked
            if ($form->get('saveAndSend')->isClicked()) {
                $this->get('app.interview.manager')->sendScheduleEmail($interview, $data);
            }

            return $this->redirect($this->generateUrl('admissionadmin_show', array('status' => 'assigned')));
        }

        return $this->render('interview/schedule.html.twig', array(
            'form' => $form->createView(),
            'interview' => $interview,
            'application' => $application, ));
    }

    /**
     * Renders and handles the submission of the assign interview form.
     * This method is used to create a new interview, or update it, and assign it to the given application.
     * It sets the interviewer and interview schema according to the form.
     * This method is intended to be called by an Ajax request.
     *
     * @param Request $request
     * @param $id
     *
     * @return JsonResponse
     */
    public function assignAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $application = $em->getRepository('AppBundle:Application')->find($id);
        $user = $application->getUser();
        // Finds all the roles above admin in the hierarchy, used to populate dropdown menu with all admins
        $roles = $this->get('app.reversed_role_hierarchy')->getParentRoles([RoleManager::ROLE_TEAM_MEMBER]);

        $form = $this->createForm(new AssignInterviewType($roles), $application);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $application->getInterview()->setUser($user);
            $em->persist($application);
            $em->flush();

            return new JsonResponse(
                array('success' => true)
            );
        }

        return new JsonResponse(
            array(
                'form' => $this->renderView('interview/assign_interview_form.html.twig', array(
                    'form' => $form->createView(),
                )),
            )
        );
    }

    /**
     * This method has the same purpose as assignAction, but assigns a bulk of applications at once.
     * It does not use the normal form validation routine, but manually updates each application.
     * This is because in addition to the standard form fields given by assignInterviewType, a list of application ids
     * are given by the bulk form checkboxes (see admission_admin twigs).
     *
     * This method is intended to be called by an Ajax request.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function bulkAssignAction(Request $request)
    {
        // Finds all the roles above admin in the hierarchy, used to populate dropdown menu with all admins
        $roles = $this->get('app.reversed_role_hierarchy')->getParentRoles([RoleManager::ROLE_TEAM_MEMBER]);
        $form = $this->createForm(new AssignInterviewType($roles));

        if ($request->isMethod('POST')) {
            $em = $this->getDoctrine()->getManager();
            // Get the info from the form
            $data = $request->request->get('application');
            // Get objects from database
            $interviewer = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $data['interview']['interviewer']));
            $schema = $em->getRepository('AppBundle:InterviewSchema')->findOneBy(array('id' => $data['interview']['interviewSchema']));
            $applications = $em->getRepository('AppBundle:Application')->findBy(array('id' => $data['id']));

            // Update or create new interviews for all the given applications
            foreach ($applications as $application) {
                $this->get('app.interview.manager')->assignInterviewerToApplication($interviewer, $application);

                $application->getInterview()->setInterviewSchema($schema);
                $em->persist($application);
            }

            $em->flush();

            return new JsonResponse(array(
                'success' => true,
                'request' => $request->request->all(),
            ));
        }

        return new JsonResponse(array(
            'form' => $this->renderView('interview/assign_interview_form.html.twig', array(
                    'form' => $form->createView(),
                )),
        ));
    }
}
