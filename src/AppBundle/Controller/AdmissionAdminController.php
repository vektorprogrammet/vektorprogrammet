<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Application;
use AppBundle\Entity\Semester;
use AppBundle\Form\Type\ApplicationType;
use AppBundle\Role\Roles;
use AppBundle\Service\InterviewCounter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * AdmissionAdminController is the controller responsible for administrative admission actions,
 * such as showing and deleting applications.
 */
class AdmissionAdminController extends Controller
{
    /**
     * Shows the admission admin page. Shows only applications for the department of the logged in user.
     * This works as the restricted admission management method, only allowing users to manage applications within their department.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction()
    {
        $department = $this->getUser()->getDepartment();
        $semester = $department->getCurrentOrLatestSemester();

        return $this->showNewApplicationsBySemesterAction($semester);
    }

    public function showNewApplicationsBySemesterAction(Semester $semester = null)
    {
        if ($semester === null) {
            $semester = $this->getUser()->getDepartment()->getCurrentOrLatestSemester();
        }
        $department = $semester->getDepartment();

        if (!$this->isGranted(Roles::TEAM_LEADER) && $this->getUser()->getDepartment() !== $department) {
            throw $this->createAccessDeniedException();
        }

        $applications = $this->getDoctrine()->getRepository('AppBundle:Application')->findNewApplicationsBySemester($semester);

        return $this->render('admission_admin/new_applications_table.html.twig', array(
            'applications' => $applications,
            'semester' => $semester,
            'status' => 'new',
        ));
    }

    public function showAssignedApplicationsBySemesterAction(Semester $semester = null)
    {
        if ($semester === null) {
            $semester = $this->getUser()->getDepartment()->getCurrentOrLatestSemester();
        }
        $department = $semester->getDepartment();

        if (!$this->isGranted(Roles::TEAM_LEADER) && $this->getUser()->getDepartment() !== $department) {
            throw $this->createAccessDeniedException();
        }

        $applicationRepo = $this->getDoctrine()->getRepository('AppBundle:Application');

        $applications = $applicationRepo->findAssignedApplicants($semester);
        $interviewDistributions = $this->get('app.interview_counter')->createInterviewDistributions($applications, $semester);

        $cancelledApplications = $applicationRepo->findCancelledApplicants($semester);

        $applicationsAssignedToUser = $applicationRepo->findAssignedByUserAndSemester($this->getUser(), $semester);

        return $this->render('admission_admin/assigned_applications_table.html.twig', array(
            'status' => 'assigned',
            'applications' => $applications,
            'semester' => $semester,
            'interviewDistributions' => $interviewDistributions,
            'cancelledApplications' => $cancelledApplications,
            'yourApplications' => $applicationsAssignedToUser,
        ));
    }

    public function showInterviewedApplicationsBySemesterAction(Semester $semester = null)
    {
        if ($semester === null) {
            $semester = $this->getUser()->getDepartment()->getCurrentOrLatestSemester();
        }
        $department = $semester->getDepartment();

        if (!$this->isGranted(Roles::TEAM_LEADER) && $this->getUser()->getDepartment() !== $department) {
            throw $this->createAccessDeniedException();
        }

        $applications = $this->getDoctrine()->getRepository('AppBundle:Application')->findInterviewedApplicants(null, $semester);

        $counter = $this->get('app.interview_counter');

        return $this->render('admission_admin/interviewed_applications_table.html.twig', array(
            'status' => 'interviewed',
            'applications' => $applications,
            'semester' => $semester,
            'yes' => $counter->count($applications, InterviewCounter::YES),
            'no' => $counter->count($applications, InterviewCounter::NO),
            'maybe' => $counter->count($applications, InterviewCounter::MAYBE),
        ));
    }

    public function showExistingApplicationsBySemesterAction(Semester $semester = null)
    {
        if ($semester === null) {
            $semester = $this->getUser()->getDepartment()->getCurrentOrLatestSemester();
        }
        $department = $semester->getDepartment();

        if (!$this->isGranted(Roles::TEAM_LEADER) && $this->getUser()->getDepartment() !== $department) {
            throw $this->createAccessDeniedException();
        }

        $applications = $this->getDoctrine()->getRepository('AppBundle:Application')->findExistingApplicants($department, $semester);

        return $this->render('admission_admin/existing_assistants_applications_table.html.twig', array(
            'status' => 'existing',
            'applications' => $applications,
            'semester' => $semester,
        ));
    }

    /**
     * Deletes the given application.
     * This method is intended to be called by an Ajax request.
     *
     * @param Application $application
     *
     * @return JsonResponse
     */
    public function deleteApplicationByIdAction(Application $application)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($application);
        $em->flush();

        return new JsonResponse([
            'success' => true,
        ]);
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
        // Get the ids from the form
        $applicationIds = $request->request->get('application')['id'];

        $em = $this->getDoctrine()->getManager();

        // Delete the applications
        foreach ($applicationIds as $id) {
            $application = $this->getDoctrine()->getRepository('AppBundle:Application')->find($id);

            if ($application !== null) {
                $em->remove($application);
            }
        }

        $em->flush();

        return new JsonResponse([
            'success' => true,
        ]);
    }

    public function createApplicationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $department = $this->getUser()->getDepartment();
        $currentSemester = $em->getRepository('AppBundle:Semester')->findCurrentSemesterByDepartment($department);

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

            $this->addFlash('admission-notice', 'Søknaden er registrert.');

            return $this->redirectToRoute('register_applicant', array('id' => $department->getId()));
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

    public function showTeamInterestAction(Semester $semester)
    {
        $user = $this->getUser();
        $department = $semester->getDepartment();

        if (!$this->isGranted(Roles::ADMIN) && $user->getDepartment() !== $department) {
            throw $this->createAccessDeniedException();
        }

        $teamInterest = $this->getDoctrine()->getRepository('AppBundle:Application')->findApplicationByTeamInterestAndSemester($semester);
        $teams = $this->getDoctrine()->getRepository('AppBundle:Team')->findByTeamInterestAndSemester($semester);

        return $this->render('admission_admin/teamInterest.html.twig', array(
            'teamInterest' => $teamInterest,
            'semester' => $semester,
            'teams' => $teams,
        ));
    }
}
