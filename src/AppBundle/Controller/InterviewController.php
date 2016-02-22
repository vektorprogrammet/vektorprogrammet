<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Interview;
use AppBundle\Entity\InterviewAnswer;
use AppBundle\Entity\InterviewSchema;
use AppBundle\Entity\Application;
use AppBundle\Form\Type\ScheduleInterviewType;
use AppBundle\Form\Type\InterviewSchemaType;
use AppBundle\Form\Type\InterviewType;
use AppBundle\Form\Type\AssignInterviewType;

/**
 * InterviewController is the controller responsible for interview actions,
 * such as showing, assigning and conducting interviews.
 *
 * @package AppBundle\Controller
 */
class InterviewController extends Controller
{
    /**
     * Shows and handles the submission of the interview form.
     * The rendered page is the page used to conduct interviews.
     *
     * @param Request $request
     * @param Interview $interview
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function conductAction(Request $request, Interview $interview)
    {

        // Only admin and above, or the assigned interviewer should be able to conduct an interview
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') &&
            !$interview->isInterviewer($this->getUser())) {
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        // If the interview has not yet been conducted, create up to date answer objects for all questions in schema
        if(!$interview->getInterviewed()) {
            foreach($interview->getInterviewSchema()->getInterviewQuestions() as $interviewQuestion) {
                // Create a new answer object for the question
                $answer = new InterviewAnswer();
                $answer->setInterview($interview);
                $answer->setInterviewQuestion($interviewQuestion);

                // Add the answer object to the interview
                $interview->addInterviewAnswer($answer);
            }

            // If the interview is deleted after it has been conducted, and a new is made, we need to check for score and practical in the database,
            // which are connected to application statistics (score, practical and statistics are stored in the database even if application/interview is deleted)
            $interview->setInterviewScore($interview->getApplication()->getStatistic()->getInterviewScore());
            $interview->setInterviewPractical($interview->getApplication()->getStatistic()->getInterviewPractical());
        }

        $form = $this->createForm(new interviewType(), $interview);
        $form->handleRequest($request);

        if ($form->isValid()) {
            // Set interviewed to true if the form is valid
            $interview->setInterviewed(true);

            // Set the conducted datetime to now
            $interview->setConducted(new \DateTime());

            // Link the application statistic object to the practical and score objects
            $interview->getInterviewScore()->setApplicationStatistic($interview->getApplication()->getStatistic());
            $interview->getInterviewPractical()->setApplicationStatistic($interview->getApplication()->getStatistic());

            // Persist
            $em->persist($interview);
            $em->flush();

            return $this->redirect($this->generateUrl('admissionadmin_show', array('status' => 'interviewed')));
        }
        return $this->render('interview/conduct.html.twig', array(
            'interview' => $interview,
            'form' => $form->createView()
        ));
    }

    /**
     * Shows the given interview.
     *
     * @param Interview $interview
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Interview $interview)
    {
        // Only accessible for admin and above, or team members belonging to the same department as the interview
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') &&
            !$interview->getApplication()->isSameDepartment($this->getUser())) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('interview/show.html.twig', array('interview' => $interview));
    }

    /**
     * Deletes the given interview.
     * This method is intended to be called by an Ajax request.
     *
     * @param $id
     * @return JsonResponse
     */
    public function deleteInterviewAction($id){
        try {
            // Only the highest user role should be able to delete an interview
            if ($this->get('security.authorization_checker')->isGranted('ROLE_HIGHEST_ADMIN')) {

                $em = $this->getDoctrine()->getManager();
                // Find the interview by ID
                $interview = $em->getRepository('AppBundle:Interview')->find($id);

                // Delete the interview
                $em->remove($interview);
                $em->flush();

                // AJAX response
                $response['success'] = true;
            }
            else {
                // Send a respons to AJAX
                $response['success'] = false;
                $response['cause'] = 'Ikke tilstrekkelige rettigheter.';
            }
        }
        catch (\Exception $e) {
            // Send a respons to AJAX
            return new JsonResponse([
                'success' => false,
                'code'    => $e->getCode(),
                'cause' => 'En exception oppstod. Vennligst kontakt IT-ansvarlig.',
                // 'cause' => $e->getMessage(), if you want to see the exception message.
            ]);
        }

        // Response to ajax
        return new JsonResponse( $response );
    }

    /**
     * Deletes a bulk of interviews.
     * Takes a list of application ids through a form POST request, and deletes the interviews associated with them.
     *
     * This method is intended to be called by an Ajax request.
     *
     * @param $id
     * @return JsonResponse
     */
    public function bulkDeleteInterviewAction(Request $request){
        try {
            // Only the highest user role should be able to delete an interview
            if ($this->get('security.authorization_checker')->isGranted('ROLE_HIGHEST_ADMIN')) {

                // Get the ids from the form
                $applicationIds = $request->request->get('application')['id'];

                // Get the application objects
                $em = $this->getDoctrine()->getEntityManager();
                $applications = $em->getRepository('AppBundle:Application')->findById($applicationIds);

                // Delete the interviews
                foreach($applications as $application) {
                    $interview = $application->getInterview();
                    if($interview) {
                        $em->remove($interview);
                    }
                }
                $em->flush();

                // AJAX response
                $response['success'] = true;
            }
            else {
                // Send a respons to AJAX
                $response['success'] = false;
                $response['cause'] = 'Ikke tilstrekkelige rettigheter.';
            }
        }
        catch (\Exception $e) {
            // Send a respons to AJAX
            return new JsonResponse([
                'success' => false,
                'code'    => $e->getCode(),
                'cause' => 'En exception oppstod. Vennligst kontakt IT-ansvarlig.',
                // 'cause' => $e->getMessage(), if you want to see the exception message.
            ]);
        }

        // Response to ajax
        return new JsonResponse( $response );
    }

    /**
     * Shows and handles the submission of the create interview schema form.
     * Uses the same form as the edit action.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createSchemaAction(Request $request)
    {
        $schema = new InterviewSchema();

        $form = $this->createForm(new InterviewSchemaType(), $schema);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($schema);
            $em->flush();

            // Need some form of redirect. Will cause wrong database entries if the form is rendered again
            // after a valid submit, without remaking the form with up to date question objects from the database.
            return $this->redirect($this->generateUrl('interview_schema'));
        }

        return $this->render('interview/schema.html.twig', array('form' => $form->createView()));
    }

    /**
     * Shows and handles the submission of the edit interview schema form.
     * Uses the same form as the create action.
     *
     * @param Request $request
     * @param InterviewSchema $schema
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editSchemaAction(Request $request, InterviewSchema $schema)
    {
        $form = $this->createForm(new InterviewSchemaType(), $schema);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($schema);
            $em->flush();

            // Need some form of redirect. Will cause wrong database entries if the form is rendered again
            // after a valid submit, without remaking the form with up to date question objects from the database.
            return $this->redirect($this->generateUrl('interview_schema'));
        }

        return $this->render('interview/schema.html.twig', array('form' => $form->createView()));
    }

    /**
     * Shows the interview schemas page.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showSchemasAction()
    {
        $schemas = $this->getDoctrine()->getRepository('AppBundle:InterviewSchema')->findAll();

        return $this->render('interview/schemas.html.twig', array('schemas' => $schemas));
    }

    /**
     * Deletes the given interview schema.
     * This method is intended to be called by an Ajax request.
     *
     * @param InterviewSchema $schema
     * @return JsonResponse
     */
    public function deleteSchemaAction(InterviewSchema $schema)
    {
        try {
            if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($schema);
                $em->flush();

                $response['success'] = true;
            } else {
                $response['success'] = false;
                $response['cause'] = 'Ikke tilstrekkelig rettigheter';
            }
        } catch (\Exception $e) {
            $response = ['success' => false,
                'code' => $e->getCode(),
                'cause' => 'Det oppstod en feil.'
            ];
        }

        return new JsonResponse($response);
    }

    /**
     * Shows and handles the submission of the schedule interview form.
     * This method can also send an email to the applicant with the info from the submitted form.
     *
     * @param Request $request
     * @param Interview $interview
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function scheduleAction(Request $request, Interview $interview)
    {
        // Only admin and above, or the assigned interviewer should be able to book an interview
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') &&
            !$interview->isInterviewer($this->getUser())) {
            throw $this->createAccessDeniedException();
        }

        // Set the default data for the form
        $defaultData = array(
            'datetime' => $interview->getScheduled(),
            'message' => 'Hei, vi har satt opp et intervju for deg angående opptak til vektorprogrammet. ' .
            'Vennligst gi beskjed til meg hvis tidspunktet ikke passer.',
            'from' => $interview->getInterviewer()->getEmail(),
            'to' => $interview->getApplication()->getEmail()
        );

        $form = $this->createForm(new ScheduleInterviewType, $defaultData);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

            // Update the scheduled time for the interview
            $interview->setScheduled($data['datetime']);
            $em = $this->getDoctrine()->getManager();
            $em->persist($interview);
            $em->flush();

            // Send email if the send button was clicked
            if($form->get('saveAndSend')->isClicked()) {
                $mailer = $this->get('mailer');
                $message = \Swift_Message::newInstance()
                    ->setSubject('Intervju for vektorprogrammet')
                    ->setFrom('opptak@vektorprogrammet.no')
                    ->setTo($data['to'])
                    ->setReplyTo($data['from'])
                    ->setBody(
                        $this->renderView('interview/email.html.twig',
                            array('message' => $data['message'],
                                'datetime' => $data['datetime'],
                                'fromName' => $interview->getInterviewer()->getFirstName() . " " . $interview->getInterviewer()->getLastName(),
                                'fromMail' => $data['from'],
                                'fromPhone' => $interview->getInterviewer()->getPhone()
                                )
                        ),
                        'text/html'
                    );
                $mailer->send($message);
            }
            return $this->redirect($this->generateUrl('admissionadmin_show', array('status' => 'assigned')));
        }

        return $this->render('interview/schedule.html.twig', array(
            'form' => $form->createView(),
            'interview' => $interview));
    }

    /**
     * Renders and handles the submission of the assign interview form.
     * This method is used to create a new interview, or update it, and assign it to the given application.
     * It sets the interviewer and interview schema according to the form.
     * This method is intended to be called by an Ajax request.
     *
     * @param Request $request
     * @param Application $application
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function assignAction(Request $request, Application $application)
    {
        // Finds all the roles above admin in the hierarchy, used to populate dropdown menu with all admins
        $roles = $this->get('app.reversed_role_hierarchy')->getParentRoles(['ROLE_ADMIN']);

        $form = $this->createForm(new AssignInterviewType($roles), $application);

        $form->handleRequest($request);

        if($form->isValid()) {
            $application->getInterview()->setInterviewed(false);
            $em = $this->getDoctrine()->getManager();
            $em->persist($application);
            $em->flush();

            return new JsonResponse(
                array('success' => true)
            );
        }

        return new JsonResponse(
            array(
                'form' => $this->renderView('interview/assign_interview_form.html.twig', array(
                    'form' => $form->createView()
                ))
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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function bulkAssignAction(Request $request)
    {
        // Finds all the roles above admin in the hierarchy, used to populate dropdown menu with all admins
        $roles = $this->get('app.reversed_role_hierarchy')->getParentRoles(['ROLE_ADMIN']);
        $form = $this->createForm(new AssignInterviewType($roles));

        if($request->isMethod('POST')) {
            $em = $this->getDoctrine()->getManager();

            // Get the info from the form
            $interviewerId = $request->request->get('application')['interview']['interviewer'];
            $schemaId = $request->request->get('application')['interview']['interviewSchema'];
            $applicationIds = $request->request->get('application')['id'];

            // Get objects from database
            $interviewer = $em->getRepository('AppBundle:User')->findOneById($interviewerId);
            $schema = $em->getRepository('AppBundle:InterviewSchema')->findOneById($schemaId);
            $applications = $em->getRepository('AppBundle:Application')->findById($applicationIds);

            // Update or create new interviews for all the given applications
            foreach($applications as $application) {
                $interview = $application->getInterview();
                if(!$interview) {
                    $interview = new Interview();
                    $application->setInterview($interview);
                    $interview->setInterviewed(false);
                }
                $interview->setInterviewer($interviewer);
                $interview->setInterviewSchema($schema);
                $em->persist($application);
            }

            $em->flush();

            return new JsonResponse(
                array('success' => true)
            );
        }

        return new JsonResponse(
            array(
                'form' => $this->renderView('interview/assign_interview_form.html.twig', array(
                    'form' => $form->createView()
                ))
            )
        );
    }
}
