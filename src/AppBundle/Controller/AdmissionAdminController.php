<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Application;
use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use AppBundle\Form\Type\ApplicationType;
use AppBundle\Role\Roles;
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

    public function showNewApplicationsBySemesterAction(Semester $semester)
    {
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

    public function showAssignedApplicationsBySemesterAction(Semester $semester)
    {
        $department = $semester->getDepartment();

        if (!$this->isGranted(Roles::TEAM_LEADER) && $this->getUser()->getDepartment() !== $department) {
            throw $this->createAccessDeniedException();
        }

        return $this->renderApplicants($semester,  "assigned");
    }

    public function showInterviewedApplicationsBySemesterAction(Semester $semester)
    {
        $department = $semester->getDepartment();

        if (!$this->isGranted(Roles::TEAM_LEADER) && $this->getUser()->getDepartment() !== $department) {
            throw $this->createAccessDeniedException();
        }

        return $this->renderApplicants($semester,  "interviewed");
    }

    public function showExistingApplicationsBySemesterAction(Semester $semester)
    {
        $department = $semester->getDepartment();

        if (!$this->isGranted(Roles::TEAM_LEADER) && $this->getUser()->getDepartment() !== $department) {
            throw $this->createAccessDeniedException();
        }

        return $this->renderApplicants($semester, "existing");
    }

    private function renderApplicants(Semester $semester, string $status)
    {

        $department = $semester->getDepartment();

        $user = $this->getUser();

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
                throw $this->createNotFoundException();
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
            'semester' => $semester,
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
}
