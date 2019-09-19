<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Receipt;
use AppBundle\Entity\Feedback;
use AppBundle\Form\Type\FeedbackType;
use AppBundle\Service\AdmissionStatistics;
use AppBundle\Service\Sorter;
use AppBundle\Utils\ReceiptStatistics;

class WidgetController extends BaseController
{
    public function interviewsAction()
    {
        $department = $this->getDepartmentOrThrow404();
        $semester = $this->getSemesterOrThrow404();
        $admissionPeriod = $this->getDoctrine()->getRepository('AppBundle:AdmissionPeriod')
            ->findOneByDepartmentAndSemester($department, $semester);
        $applicationsAssignedToUser = [];

        if ($admissionPeriod !== null) {
            $applicationRepo = $this->getDoctrine()->getRepository('AppBundle:Application');
            $applicationsAssignedToUser = $applicationRepo->findAssignedByUserAndAdmissionPeriod($this->getUser(), $admissionPeriod);
        }

        return $this->render('widgets/interviews_widget.html.twig', ['applications' => $applicationsAssignedToUser]);
    }

    public function receiptsAction()
    {
        $usersWithReceipts = $this->getDoctrine()->getRepository('AppBundle:User')->findAllUsersWithReceipts();
        $sorter = $this->container->get(Sorter::class);

        $sorter->sortUsersByReceiptSubmitTime($usersWithReceipts);
        $sorter->sortUsersByReceiptStatus($usersWithReceipts);

        $pendingReceipts = $this->getDoctrine()->getRepository('AppBundle:Receipt')->findByStatus(Receipt::STATUS_PENDING);
        $pendingReceiptStatistics = new ReceiptStatistics($pendingReceipts);

        $hasReceipts = !empty($pendingReceipts);

        return $this->render('widgets/receipts_widget.html.twig', [
            'users_with_receipts' => $usersWithReceipts,
            'statistics' => $pendingReceiptStatistics,
            'has_receipts' => $hasReceipts,
        ]);
    }

    public function applicationGraphAction()
    {
        $department = $this->getDepartmentOrThrow404();
        $semester = $this->getSemesterOrThrow404();

        $admissionStatistics = $this->get(AdmissionStatistics::class);

        $admissionPeriod = $this->getDoctrine()->getRepository('AppBundle:AdmissionPeriod')
            ->findOneByDepartmentAndSemester($department, $semester);
        $applicationsInSemester = [];
        if ($admissionPeriod !== null) {
            $applicationsInSemester = $this->getDoctrine()
                ->getRepository('AppBundle:Application')
                ->findByAdmissionPeriod($admissionPeriod);
        }
        $appData = $admissionStatistics->generateCumulativeGraphDataFromApplicationsInSemester($applicationsInSemester, $semester);

        return $this->render('widgets/application_graph_widget.html.twig', [
            'appData' => $appData,
            'semester' => $semester,
        ]);
    }


    public function availableSurveysAction()
    {
        $semester = $this->getSemesterOrThrow404();

        $surveys = $this->getDoctrine()
            ->getRepository('AppBundle:Survey')
            ->findAllNotTakenByUserAndSemester($this->getUser(), $semester);


        return $this->render('widgets/available_surveys_widget.html.twig', [
            'availableSurveys' => $surveys,
        ]);
    }
    public function FeedbackAction()
    {
        $feedback = new Feedback;
        $user = $this->getUser();
        $form = $this->createForm(FeedBackType::class, $feedback);

        return $this->render('widgets/feedback_widget.html.twig', array(
            'title' => 'Feedback',
            'form' => $form->createView()
        ));

    }
}
