<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Application;
use AppBundle\Entity\Interview;
use AppBundle\Event\InterviewConductedEvent;
use AppBundle\Event\InterviewEvent;
use AppBundle\Form\InterviewNewTimeType;
use AppBundle\Form\Type\AddCoInterviewerType;
use AppBundle\Form\Type\ApplicationInterviewType;
use AppBundle\Form\Type\AssignInterviewType;
use AppBundle\Form\Type\CancelInterviewConfirmationType;
use AppBundle\Form\Type\ScheduleInterviewType;
use AppBundle\Role\Roles;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
        $department = $this->getUser()->getDepartment();
        $teams = $this->getDoctrine()->getRepository('AppBundle:Team')->findByDepartment($department);

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
            'teams' => $teams,
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $isNewInterview = !$interview->getInterviewed();
            $interview->setCancelled(false);

            dump($form->getData());

            $em = $this->getDoctrine()->getManager();
            $em->persist($interview);
            $em->flush();
            if ($isNewInterview && $form->get('saveAndSend')->isClicked()) {
                $interview->setInterviewed(true);
                $em->persist($interview);
                $em->flush();

                $this->get('event_dispatcher')->dispatch(InterviewConductedEvent::NAME, new InterviewConductedEvent($application));
            }

            return $this->redirectToRoute('applications_show_interviewed_by_semester', array('id' => $application->getSemester()->getId()));
        }

        return $this->render('interview/conduct.html.twig', array(
            'application' => $application,
            'department' => $department,
            'teams' => $teams,
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

        return $this->redirectToRoute('applications_show_assigned');
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
            throw $this->createNotFoundException('Interview not found.');
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
     */
    public function deleteInterviewAction(Interview $interview)
    {
        $application = $this->getDoctrine()->getRepository('AppBundle:Application')->findOneBy(array('interview' => $interview));
        $application->setInterview(null);

        $em = $this->getDoctrine()->getManager();
        $em->persist($application);
        $em->remove($interview);
        $em->flush();

        // AJAX response
        return new JsonResponse(array(
            'success' => true,
        ));
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
            $application->setInterview(null);
        }
        $em->flush();

        // AJAX response
        return new JsonResponse(array(
            'success' => true,
        ));
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

        $data = $form->getData();
        $mapLink = $data['mapLink'];
        if ($form->isSubmitted()) {
            if ($mapLink && !(strpos($mapLink, 'http')===0)) {
                $mapLink='http://' . $mapLink;
            }
        }
        $invalidMapLink = $form->isSubmitted() && !empty($mapLink) && !$this->validateLink($mapLink);
        if ($invalidMapLink) {
            $this->addFlash('error', 'Kartlinken er ikke gyldig');
        } elseif ($form->isValid()) {
            $interview->generateAndSetResponseCode();

            // Update the scheduled time for the interview
            $interview->setScheduled($data['datetime']);
            $interview->setRoom($data['room']);

            $interview->setMapLink($mapLink);
            $interview->resetStatus();

            if ($form->get('preview')->isClicked()) {
                return $this->render('interview/preview.html.twig', array(
                    'interview' => $interview,
                    'data' => $data,
                ));
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($interview);
            $em->flush();

            // Send email if the send button was clicked
            if ($form->get('saveAndSend')->isClicked()) {
                $this->get('event_dispatcher')->dispatch(InterviewEvent::SCHEDULE, new InterviewEvent($interview, $data));
            }

            return $this->redirectToRoute('applications_show_assigned_by_semester', array('id' => $application->getSemester()->getId()));
        }

        return $this->render('interview/schedule.html.twig', array(
            'form' => $form->createView(),
            'interview' => $interview,
            'application' => $application, ));
    }

    private function validateLink($link)
    {
        if (empty($link)) {
            return false;
        }

        try {
            $headers = get_headers($link);
            $statusCode = intval(explode(" ", $headers[0])[1]);
        } catch (\Exception $e) {
            return false;
        }

        return $statusCode < 400;
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
        $roles = $this->get('app.reversed_role_hierarchy')->getParentRoles([Roles::TEAM_MEMBER]);

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
        $roles = $this->get('app.reversed_role_hierarchy')->getParentRoles([Roles::TEAM_MEMBER]);
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

    /**
     * @param Interview $interview
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function acceptByResponseCodeAction(Interview $interview)
    {
        $interview->acceptInterview();
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($interview);
        $manager->flush();

        $formattedDate = $interview->getScheduled()->format('d. M');
        $formattedTime = $interview->getScheduled()->format('H:i');
        $room = $interview->getRoom();
        $this->addFlash('title', 'Akseptert!');
        $this->addFlash('message', "Takk for at du aksepterte intervjutiden. Da sees vi $formattedDate klokka $formattedTime i $room!");
        return $this->redirectToRoute('confirmation');
    }

    /**
     * @param Request $request
     * @param Interview $interview
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function requestNewTimeAction(Request $request, Interview $interview)
    {
        if (!$interview->isPending()) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(new InterviewNewTimeType(), $interview, array(
            "validation_groups" => array("newTimeRequest")
        ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $interview->requestNewTime();
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($interview);
            $manager->flush();

            $this->get('app.interview.manager')->sendRescheduleEmail($interview);

            $this->addFlash('title', 'Notert');
            $this->addFlash('message', 'Vi tar kontakt med deg når vi har funnet en ny intervjutid.');
            return $this->redirectToRoute('confirmation');
        }

        return $this->render('interview/request_new_time.html.twig', array(
            'interview' => $interview,
            'form' => $form->createView()
        ));
    }

    /**
     * @param Interview $interview
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function respondAction(Interview $interview)
    {
        if (!$interview->isPending()) {
            throw $this->createNotFoundException();
        }

        return $this->render('interview/response.html.twig', array(
            'interview' => $interview,
        ));
    }

    /**
     * @param Request   $request
     * @param Interview $interview
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cancelByResponseCodeAction(Request $request, Interview $interview)
    {
        if (!$interview->isPending()) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(new CancelInterviewConfirmationType());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            $interview->setCancelMessage($data['message']);
            $interview->cancel();
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($interview);
            $manager->flush();

            $this->get('app.interview.manager')->sendCancelEmail($interview);

            $this->addFlash('title', 'Kansellert');
            $this->addFlash('message', 'Du har kansellert intervjuet ditt.');
            return $this->redirectToRoute('confirmation');
        }

        return $this->render('interview/response_confirm_cancel.html.twig', array(
            'interview' => $interview,
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Request $request
     * @param Interview $interview
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editStatusAction(Request $request, Interview $interview)
    {
        $status = intval($request->get('status'));
        try {
            $interview->setStatus($status);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException();
        }
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->redirectToRoute('interview_schedule',
            ['id' => $interview->getApplication()->getId()]);
    }

    public function assignCoInterviewerAction(Interview $interview)
    {
        if ($this->getUser() != $interview->getInterviewer()) {
            $interview->setCoInterviewer($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($interview);
            $em->flush();
            $this->get('event_dispatcher')->dispatch(InterviewEvent::COASSIGN, new InterviewEvent($interview));
        }
        return $this->redirectToRoute('applications_show_assigned');
    }

    public function adminAssignCoInterviewerAction(Request $request, Interview $interview)
    {
        $semester = $interview->getApplication()->getSemester();
        $teamUsers = $this->getDoctrine()->getRepository('AppBundle:User')
            ->findUsersWithTeamMembershipInSemester($semester);
        $form = $this->createForm(new AddCoInterviewerType($teamUsers));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            $user = $data['user'];
            $interview->setCoInterviewer($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($interview);
            $em->flush();
            return $this->redirectToRoute('applications_show_assigned');
        }

        return $this->render('interview/assign_co_interview_form.html.twig', array(
            'form' => $form->createView(),
            'interview' => $interview
        ));
    }

    public function clearCoInterviewerAction(Interview $interview)
    {
        $interview->setCoInterviewer(null);
        $em = $this->getDoctrine()->getManager();
        $em->persist($interview);
        $em->flush();
        return $this->redirectToRoute('applications_show_assigned');
    }
}
