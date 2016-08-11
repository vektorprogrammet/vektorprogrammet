<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Semester;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use DateTime;

class ControlPanelController extends Controller
{
    public function showAction()
    {
        $departments = $this->getDoctrine()->getRepository('AppBundle:Department')->findAll();

        // Return the view to be rendered
        return $this->render('control_panel/index.html.twig', array(
            'departments' => $departments,
        ));
    }

    public function showSBSAction()
    {
        $em = $this->getDoctrine()->getManager();

        $department = $this->get('security.token_storage')->getToken()->getUser()->getFieldOfStudy()->getDepartment();
        $semester = $em->getRepository('AppBundle:Semester')->findCurrentSemesterByDepartment($department->getId());

        $step = 0;
        $interviewedAssistantsCount = 0;
        $assignedInterviewsCount = 0;
        $totalAssistantsCount = 0;
        $totalApplicationsCount = 0;
        $admissionTimeLeft = 0;
        $timeToAdmissionStart = 0;

        if (!is_null($semester)) {
            $applicationRepository = $this->getDoctrine()->getRepository('AppBundle:Application');
            $interviewedAssistantsCount = count($applicationRepository->findInterviewedApplicants($department, $semester));
            $assignedInterviewsCount = count($applicationRepository->findAssignedApplicants($department, $semester));

            $assistantHistoryRepository = $this->getDoctrine()->getRepository('AppBundle:AssistantHistory');
            $totalAssistantsCount = count($assistantHistoryRepository->findAssistantHistoriesByDepartment($department, $semester));
            $step = $this->determineCurrentStep($semester, $interviewedAssistantsCount, $assignedInterviewsCount, $totalAssistantsCount);

            $totalApplicationsCount = count($this->getDoctrine()->getRepository('AppBundle:Application')->findBy(array('semester' => $semester)));

            if ($step >= 1 && $step < 2) {
                $timeToAdmissionStart = intval(ceil(($semester->getAdmissionStartDate()->getTimestamp() - (new \DateTime())->getTimestamp()) / 3600));
            } elseif ($step >= 2 && $step < 3) {
                $admissionTimeLeft = intval(ceil(($semester->getAdmissionEndDate()->getTimestamp() - (new \DateTime())->getTimestamp()) / 3600));
            }
        }

        // Return the view to be rendered
        return $this->render('control_panel/sbs.html.twig', array(
            'step' => $step,
            'semester' => $semester,
            'interviewedAssistantsCount' => $interviewedAssistantsCount,
            'totalInterviewsCount' => $assignedInterviewsCount + $interviewedAssistantsCount,
            'totalAssistantsCount' => $totalAssistantsCount,
            'totalApplicationsCount' => $totalApplicationsCount,
            'admissionTimeLeft' => $admissionTimeLeft,
            'timeToAdmissionStart' => $timeToAdmissionStart,
        ));
    }

    public function determineCurrentStep(Semester $semester, $interviewedAssistantsCount, $assignedInterviewsCount, $totalAssistantsCount)
    {
        $today = new DateTime('now');

        // Step 1 Before Admission
        if ($today < $semester->getAdmissionStartDate() && $today > $semester->getSemesterStartDate()) {
            return 1 + ($today->format('U') - $semester->getSemesterStartDate()->format('U')) / ($semester->getAdmissionStartDate()->format('U') - $semester->getSemesterStartDate()->format('U'));
        }

        // Step 2 Admission has started
        if ($today < $semester->getAdmissionEndDate() && $today > $semester->getAdmissionStartDate()) {
            return 2 + ($today->format('U') - $semester->getAdmissionStartDate()->format('U')) / ($semester->getAdmissionEndDate()->format('U') - $semester->getAdmissionStartDate()->format('U'));
        }

        // Step 3 Interviewing
        // No interviews are assigned yet
        if ($assignedInterviewsCount == 0 && $interviewedAssistantsCount == 0) {
            return 3;
        } // There are interviews left to conduct
        elseif ($assignedInterviewsCount > 0) {
            return 3 + $interviewedAssistantsCount / ($assignedInterviewsCount + $interviewedAssistantsCount);
        }

        // Step 4 Distribute to schools
        // All interviews are conducted, but no one has been accepted yet
        if ($totalAssistantsCount == 0) {
            return 4;
        }

        // Step 5 Operating phase
        if ($today < $semester->getSemesterEndDate() && $today > $semester->getAdmissionEndDate()) {
            return 5 + ($today->format('U') - $semester->getAdmissionEndDate()->format('U')) / ($semester->getSemesterEndDate()->format('U') - $semester->getAdmissionEndDate()->format('U'));
        }

        // Something is wrong
        return -1;
    }
}
