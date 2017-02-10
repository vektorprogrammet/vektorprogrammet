<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Application;
use AppBundle\Form\Type\ApplicationType;
use AppBundle\Form\Type\NewUserType;
use AppBundle\Role\Roles;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * AdmissionAdminController is the controller responsible for administrative admission actions,
 * such as showing and deleting applications.
 */
class AdmissionAdminController extends Controller
{
    // The name of the default role a new user is given.
    const NEW_USER_ROLE = 'User';

    /**
     * Shows the admission admin page. Shows only applications for the department of the logged in user.
     * This works as the restricted admission management method, only allowing users to manage applications within their department.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request)
    {
        return $this->renderApplicants($request);
    }

    /**
     * Shows the admission admin page with applications from the given department.
     * This is the method is only accessible by users with sufficient rights to manage all departments.
     *
     * @param Request $request
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showApplicationsByDepartmentAction(Request $request, $id)
    {
        return $this->renderApplicants($request, $id);
    }

    private function renderApplicants(Request $request, $departmentId = null)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        // Get query strings for filtering applications
        $status = $request->query->get('status', 'new');

        if ($departmentId === null) {
            // Finds the department for the current logged in user
            $department = $this->get('security.token_storage')->getToken()->getUser()->getFieldOfStudy()->getDepartment();
        } else {
            if (!$this->isGranted(Roles::TEAM_LEADER) && $user->getDepartment()->getId() !== (int) $departmentId) {
                throw $this->createAccessDeniedException();
            }
            $department = $this->getDoctrine()->getRepository('AppBundle:Department')->find($departmentId);
        }

        $semesterId = $request->query->get('semester', null);
        if ($semesterId === null) {
            $semester = $this->getDoctrine()->getRepository('AppBundle:Semester')->findCurrentSemesterByDepartment($department);
        } else {
            $semester = $this->getDoctrine()->getRepository('AppBundle:Semester')->find($semesterId);
        }

        $em = $this->getDoctrine();

        if ($semester === null) {
            try {
                $semester = $em->getRepository('AppBundle:Semester')->findLatestSemesterByDepartmentId($department->getId());
            } catch (NoResultException $e) {
                return $this->render('error/no_semester.html.twig', array('department' => $department));
            }
        } else {
            $semester = $em->getRepository('AppBundle:Semester')->find($semester);
        }
        // Finds the name of the chosen semester. If no semester chosen display 'Alle'
        $semesterName = is_null($semester) ? 'Alle' : $semester->getName();

        // Finds all the departments
        $allDepartments = $this->getDoctrine()->getRepository('AppBundle:Department')->findAll();

        // Find all the semesters associated with the department
        $semesters = $this->getDoctrine()->getRepository('AppBundle:Semester')->findAllSemestersByDepartment($department);

        // Finds the applicants for the given department filtered by interview status and semester
        $repository = $this->getDoctrine()->getRepository('AppBundle:Application');
        $applicantsAssignedToUser = array();
        $interviewDistribution = array();
        $interviewDistributionLeft = array();
        $cancelledApplicants = array();
        /* @var Application[] $applicants */
        switch ($status) {
            case 'assigned':
                $interviewRepo = $this->getDoctrine()->getRepository('AppBundle:Interview');

                //Count completed interviews
                $interviewedApplicants = $repository->findInterviewedApplicants($department, $semester);
                foreach ($interviewedApplicants as $interviewedApplicant) {
                    $numInterviews = $interviewRepo->numberOfInterviewsByUserInSemester($interviewedApplicant->getInterview()->getInterviewer(), $semester);
                    $fullName = $interviewedApplicant->getInterview()->getInterviewer()->getFullName();
                    if (!array_key_exists($fullName, $interviewDistribution) && $numInterviews > 0) {
                        $interviewDistribution[$fullName] = $numInterviews;
                        $interviewDistributionLeft[$fullName] = 0;
                    }
                }

                //Count assigned interviews
                $applicants = $repository->findAssignedApplicants($department, $semester);
                foreach ($applicants as $applicant) {
                    $numInterviews = $interviewRepo->numberOfInterviewsByUserInSemester($applicant->getInterview()->getInterviewer(), $semester);
                    $fullName = $applicant->getInterview()->getInterviewer()->getFullName();
                    if (!array_key_exists($fullName, $interviewDistribution) && $numInterviews > 0) {
                        $interviewDistribution[$fullName] = $numInterviews;
                    }
                    if ($applicant->getInterview()->getInterviewer() == $user) {
                        $applicantsAssignedToUser[] = $applicant;
                    }
                }

                foreach ($applicants as $applicant) {
                    $fullName = $applicant->getInterview()->getInterviewer()->getFirstName().' '.$applicant->getInterview()->getInterviewer()->getLastName();
                    if (array_key_exists($fullName, $interviewDistributionLeft)) {
                        ++$interviewDistributionLeft[$fullName];
                    } else {
                        $interviewDistributionLeft[$fullName] = 1;
                    }
                }
                $cancelledApplicants = $repository->findCancelledApplicants($semester);
                arsort($interviewDistribution);
                $template = 'assigned_applications_table.html.twig';
                break;
            case 'interviewed':
                $applicants = $repository->findInterviewedApplicants($department, $semester);
                foreach ($applicants as $applicant) {
                    if ($applicant->getUser() == $user) {
                        $applicant->getInterview()->getInterviewScore()->hideScores();
                        break;
                    }
                }
                $template = 'interviewed_applications_table.html.twig';
                break;
            case 'existing':
                $applicants = $repository->findExistingApplicants($department, $semester);
                $template = 'existing_assistants_applications_table.html.twig';
                break;
            default:
                $applicants = $repository->findNewApplicants($department, $semester);
                $template = 'new_applications_table.html.twig';
                $status = 'new';
        }

        return $this->render('admission_admin/'.$template, array(
            'status' => $status,
            'applicants' => $applicants,
            'yourApplicants' => $applicantsAssignedToUser,
            'interviewDistribution' => $interviewDistribution,
            'interviewDistributionLeft' => $interviewDistributionLeft,
            'department' => $department,
            'departments' => $allDepartments,
            'semesters' => $semesters,
            'semesterName' => $semesterName,
            'numOfApplicants' => sizeof($applicants),
            'departmentName' => $department->getShortName(),
            'user' => $user,
            'cancelledApplicants' => $cancelledApplicants,
        ));
    }

    /**
     * Deletes the given application.
     * This method is intended to be called by an Ajax request.
     *
     * @param $id
     *
     * @return JsonResponse
     */
    public function deleteApplicationByIdAction($id)
    {
        try {
            if ($this->get('security.context')->isGranted('ROLE_HIGHEST_ADMIN')) {

                // This deletes the given application
                $this->deleteApplicationById($id);

                // AJAX response
                $response['success'] = true;
            } else {
                // Send a respons to AJAX
                $response['success'] = false;
                $response['cause'] = 'Ikke tilstrekkelige rettigheter.';
            }
        } catch (\Exception $e) {
            // Send a respons to AJAX
            return new JsonResponse([
                'success' => false,
                'code' => $e->getCode(),
                'cause' => 'En exception oppstod. Vennligst kontakt IT-ansvarlig.',
                // 'cause' => $e->getMessage(), if you want to see the exception message.
            ]);
        }

        // Response to ajax
        return new JsonResponse($response);
    }

    private function deleteApplicationById($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $application = $this->getDoctrine()->getRepository('AppBundle:Application')->find($id);
        if ($application->getInterview() !== null) {
            $em->remove($application->getInterview());
            $em->flush();
        }
        $em->remove($application);
        $em->flush();

        return true;
    }

    /**
     * Deletes the applications submitted as a list of ids through a form POST request.
     * This method is intended to be called by an Ajax request.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function bulkDeleteApplicationAction(Request $request)
    {
        try {
            // Get the ids from the form
            $applicationIds = $request->request->get('application')['id'];

            if ($this->get('security.context')->isGranted('ROLE_HIGHEST_ADMIN')) {
                // Delete the applications
                foreach ($applicationIds as $applicationId) {
                    $this->deleteApplicationById($applicationId);
                }
                // AJAX response
                $response['success'] = true;
            } else {
                // Send a respons to AJAX
                $response['success'] = false;
                $response['cause'] = 'Ikke tilstrekkelige rettigheter.';
            }
        } catch (\Exception $e) {
            // Send a respons to AJAX
            return new JsonResponse([
                'success' => false,
                'cause' => 'En exception oppstod. Vennligst kontakt IT-ansvarlig.',
                // 'cause' => $e->getMessage(), if you want to see the exception message.
            ]);
        }

        // Response to ajax
        return new JsonResponse($response);
    }

    /**
     * @param Request $request
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createNewUserAction(Request $request, $id)
    {
        try {
            $repositoryUser = $this->getDoctrine()->getRepository('AppBundle:User');
            $hashedNewUserCode = hash('sha512', $id, false);
            $user = $repositoryUser->findUserByNewUserCode($hashedNewUserCode);

            $form = $this->createForm(new NewUserType(), $user);

            $form->handleRequest($request);

            //Checks if the form is valid
            if ($form->isValid()) {
                //Deletes the newUserCode, so it can only be used one time.
                $user->setNewUserCode(null);

                $user->setActive('1');

                //Updates the database
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                //renders the login page, with a feedback message so that the user knows that the new password was stored.
                $feedback = 'Logg inn med din nye bruker';

                return $this->render('Login/login.html.twig', array('message' => $feedback, 'error' => null, 'last_username' => $user->getUsername()));
            }
            //Render reset_password twig with the form.
            return $this->render('new_user/create_new_user.html.twig', array(
                'form' => $form->createView(),
                'user' => $user,
            ));
        } catch (\Exception $e) {
            return $this->redirect('/');
        }
    }

    public function createApplicationAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $department = $this->get('security.token_storage')->getToken()->getUser()->getFieldOfStudy()->getDepartment();
        try {
            $currentSemester = $em->getRepository('AppBundle:Semester')->findCurrentSemesterByDepartment($department);
        } catch (NoResultException $e) {
            return $this->redirect($this->generateUrl('semesteradmin_show'));
        } catch (NonUniqueResultException $e) {
            return $this->redirect($this->generateUrl('semesteradmin_show'));
        }

        $application = new Application();
        $form = $this->createForm(ApplicationType::class, $application, array(
            'departmentId' => $department->getId(),
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $user = $em->getRepository('AppBundle:User')->findOneBy(array('email' => $application->getUser()->getEmail()));
            if ($user !== null) {
                $application->setUser($user);
            }
            $application->setSemester($currentSemester);
            $em->persist($application);
            $em->flush();

            // Send a confirmation email with a copy of the application
            $emailMessage = \Swift_Message::newInstance()
                ->setSubject('Søknad - Vektorassistent')
                ->setFrom(array($department->getEmail() => 'Vektorprogrammet'))
                ->setTo($application->getUser()->getEmail())
                ->setBody($this->renderView('admission/admission_email.html.twig', array('application' => $application)));
            $this->get('mailer')->send($emailMessage);

            $request->getSession()->getFlashBag()->add('admission-notice', 'Søknaden er registrert.');

            return $this->redirect($this->generateUrl('register_applicant', array(
                'id' => $department->getId(),
            )));
        }

        return $this->render(':admission_admin:create_application.html.twig', array(
            'department' => $department,
            'semester' => $currentSemester,
            'form' => $form->createView(),
        ));
    }

    public function showApplicationAction(Application $application)
    {
        if (!$application->getPreviousParticipation()) {
            throw $this->createNotFoundException('Søknaden finnes ikke');
        }

        return $this->render('admission_admin/application.html.twig', array(
            'application' => $application,
        ));
    }
}
