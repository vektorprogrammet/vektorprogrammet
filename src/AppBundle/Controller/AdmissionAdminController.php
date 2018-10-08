<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Application;
use AppBundle\Event\ApplicationCreatedEvent;
use AppBundle\Form\Type\ApplicationType;
use AppBundle\Role\Roles;
use AppBundle\Service\InterviewCounter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * AdmissionAdminController is the controller responsible for administrative admission actions,
 * such as showing and deleting applications.
 */
class AdmissionAdminController extends BaseController
{
    /**
     * Shows the admission admin page. Shows only applications for the department of the logged in user.
     * This works as the restricted admission management method, only allowing users to manage applications within their department.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction()
    {
        return $this->showNewApplicationsAction();
    }

    public function showNewApplicationsAction()
    {
        $semester = $this->getSemesterOrThrow404();
        $department = $this->getDepartmentOrThrow404();

        $admissionPeriod = $this->getDoctrine()
                ->getRepository('AppBundle:AdmissionPeriod')
                ->findOneByDepartmentAndSemester($department, $semester);

        if (!$this->isGranted(Roles::TEAM_LEADER) && $this->getUser()->getDepartment() !== $department) {
            throw $this->createAccessDeniedException();
        }

        $applications = [];
        if ($admissionPeriod !== null) {
            $applications = $this->getDoctrine()
                ->getRepository('AppBundle:Application')
                ->findNewApplicationsByAdmissionPeriod($admissionPeriod);
        }

        return $this->render('admission_admin/new_applications_table.html.twig', array(
            'applications' => $applications,
            'semester' => $semester,
            'department' => $department,
            'status' => 'new',
        ));
    }

    public function showAssignedApplicationsAction()
    {
        $department = $this->getDepartmentOrThrow404();
        $semester = $this->getSemesterOrThrow404();
        $admissionPeriod = $this->getDoctrine()->getRepository('AppBundle:AdmissionPeriod')
            ->findOneByDepartmentAndSemester($department, $semester);
        if (!$this->isGranted(Roles::TEAM_LEADER) && $this->getUser()->getDepartment() !== $department) {
            throw $this->createAccessDeniedException();
        }

        $applicationRepo = $this->getDoctrine()->getRepository('AppBundle:Application');

        $applications = [];
        $interviewDistributions = [];
        $cancelledApplications = [];
        $applicationsAssignedToUser = [];

        if ($admissionPeriod !== null) {
            $applications = $applicationRepo->findAssignedApplicants($admissionPeriod);
            $interviewDistributions = $this->get('app.interview_counter')
                ->createInterviewDistributions($applications, $admissionPeriod);
            $cancelledApplications = $applicationRepo->findCancelledApplicants($admissionPeriod);
            $applicationsAssignedToUser = $applicationRepo->findAssignedByUserAndAdmissionPeriod($this->getUser(), $admissionPeriod);
        }

        return $this->render('admission_admin/assigned_applications_table.html.twig', array(
            'status' => 'assigned',
            'applications' => $applications,
            'department' => $department,
            'semester' => $semester,
            'interviewDistributions' => $interviewDistributions,
            'cancelledApplications' => $cancelledApplications,
            'yourApplications' => $applicationsAssignedToUser,
        ));
    }

    public function showInterviewedApplicationsAction()
    {
        $department = $this->getDepartmentOrThrow404();
        $semester = $this->getSemesterOrThrow404();
        $admissionPeriod = $this->getDoctrine()->getRepository('AppBundle:AdmissionPeriod')
            ->findOneByDepartmentAndSemester($department, $semester);
        if (!$this->isGranted(Roles::TEAM_LEADER) && $this->getUser()->getDepartment() !== $department) {
            throw $this->createAccessDeniedException();
        }

        $applications = [];
        if ($admissionPeriod !== null) {
            $applications = $this->getDoctrine()
                ->getRepository('AppBundle:Application')
                ->findInterviewedApplicants($admissionPeriod);
        }

        $counter = $this->get('app.interview_counter');

        return $this->render('admission_admin/interviewed_applications_table.html.twig', array(
            'status' => 'interviewed',
            'applications' => $applications,
            'department' => $department,
            'semester' => $semester,
            'yes' => $counter->count($applications, InterviewCounter::YES),
            'no' => $counter->count($applications, InterviewCounter::NO),
            'maybe' => $counter->count($applications, InterviewCounter::MAYBE),
        ));
    }

    public function showExistingApplicationsAction()
    {
        $department = $this->getDepartmentOrThrow404();
        $semester = $this->getSemesterOrThrow404();
        $admissionPeriod = $this->getDoctrine()->getRepository('AppBundle:AdmissionPeriod')
            ->findOneByDepartmentAndSemester($department, $semester);

        if (!$this->isGranted(Roles::TEAM_LEADER) && $this->getUser()->getDepartment() !== $department) {
            throw $this->createAccessDeniedException();
        }
        $applications = [];
        if ($admissionPeriod !== null) {
            $applications = $this->getDoctrine()
                ->getRepository('AppBundle:Application')
                ->findExistingApplicants($admissionPeriod);
        }

        return $this->render('admission_admin/existing_assistants_applications_table.html.twig', array(
            'status' => 'existing',
            'applications' => $applications,
            'department' => $department,
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
        $currentSemester = $em->getRepository('AppBundle:Semester')->findCurrentSemester();
        $admissionPeriod = $this->getDoctrine()
            ->getRepository('AppBundle:AdmissionPeriod')
            ->findOneByDepartmentAndSemester($department, $currentSemester);
        if ($admissionPeriod === null) {
            throw new BadRequestHttpException();
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
            $application->setAdmissionPeriod($admissionPeriod);
            $em->persist($application);
            $em->flush();

            $this->addFlash('admission-notice', 'Søknaden er registrert.');

            $this->get('event_dispatcher')->dispatch(ApplicationCreatedEvent::NAME, new ApplicationCreatedEvent($application));

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

    public function showTeamInterestAction()
    {
        $user = $this->getUser();
        $department = $this->getDepartmentOrThrow404();
        $semester = $this->getSemesterOrThrow404();
        $admissionPeriod = $this->getDoctrine()->getRepository('AppBundle:AdmissionPeriod')
            ->findOneByDepartmentAndSemester($department, $semester);

        if (!$this->isGranted(Roles::ADMIN) && $user->getDepartment() !== $department) {
            throw $this->createAccessDeniedException();
        }

        $applicationsWithTeamInterest = [];
        $teams = [];
        if ($admissionPeriod !== null) {
            $applicationsWithTeamInterest = $this->getDoctrine()
                ->getRepository('AppBundle:Application')
                ->findApplicationByTeamInterestAndAdmissionPeriod($admissionPeriod);
            $teams = $this->getDoctrine()->getRepository('AppBundle:Team')->findByTeamInterestAndAdmissionPeriod($admissionPeriod);
        }

        $possibleApplicants = $this->getDoctrine()
            ->getRepository('AppBundle:TeamInterest')
            ->findBy(array('semester' => $semester, 'department' => $department));

        return $this->render('admission_admin/teamInterest.html.twig', array(
            'applicationsWithTeamInterest' => $applicationsWithTeamInterest,
            'possibleApplicants' => $possibleApplicants,
            'department' => $department,
            'semester' => $semester,
            'teams' => $teams,
        ));
    }
}
