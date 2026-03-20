<?php

namespace App\Content\Controller;

use App\Support\Controller\BaseController;
use App\Content\Infrastructure\Entity\Feedback;
use App\Operations\Infrastructure\Entity\Receipt;
use App\Admission\Infrastructure\Repository\AdmissionPeriodRepository;
use App\Admission\Infrastructure\Repository\ApplicationRepository;
use App\Content\Infrastructure\Repository\ChangeLogItemRepository;
use App\Organization\Infrastructure\Repository\DepartmentRepository;
use App\Operations\Infrastructure\Repository\ReceiptRepository;
use App\Shared\Repository\SemesterRepository;
use App\Survey\Infrastructure\Repository\SurveyRepository;
use App\Identity\Infrastructure\Repository\UserRepository;
use App\Content\Form\FeedbackType;
use App\Admission\Domain\Rules\AdmissionStatistics;
use App\Support\Sorter;
use App\Operations\Domain\Rules\ReceiptStatistics;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WidgetController extends BaseController
{
    public function __construct(
        private readonly AdmissionPeriodRepository $admissionPeriodRepo,
        private readonly ApplicationRepository $applicationRepo,
        private readonly UserRepository $userRepo,
        private readonly ReceiptRepository $receiptRepo,
        private readonly SurveyRepository $surveyRepo,
        private readonly ChangeLogItemRepository $changeLogItemRepo,
        private readonly AdmissionStatistics $admissionStatistics,
        private readonly Sorter $sorter,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    /**
     * @return Response|null
     */
    public function interviewsAction(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);
        $admissionPeriod = $this->admissionPeriodRepo
            ->findOneByDepartmentAndSemester($department, $semester);
        $applicationsAssignedToUser = [];

        if ($admissionPeriod !== null) {
            $applicationsAssignedToUser = $this->applicationRepo->findAssignedByUserAndAdmissionPeriod($this->getUser(), $admissionPeriod);
        }

        return $this->render('widgets/interviews_widget.html.twig', ['applications' => $applicationsAssignedToUser]);
    }

    public function receiptsAction()
    {
        $usersWithReceipts = $this->userRepo->findAllUsersWithReceipts();

        $this->sorter->sortUsersByReceiptSubmitTime($usersWithReceipts);
        $this->sorter->sortUsersByReceiptStatus($usersWithReceipts);

        $pendingReceipts = $this->receiptRepo->findByStatus(Receipt::STATUS_PENDING);
        $pendingReceiptStatistics = new ReceiptStatistics($pendingReceipts);

        $hasReceipts = !empty($pendingReceipts);

        return $this->render('widgets/receipts_widget.html.twig', [
            'users_with_receipts' => $usersWithReceipts,
            'statistics' => $pendingReceiptStatistics,
            'has_receipts' => $hasReceipts,
        ]);
    }

    /**
     * @return Response|null
     */
    public function applicationGraphAction(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);
        $appData = null;

        $admissionPeriod = $this->admissionPeriodRepo
            ->findOneByDepartmentAndSemester($department, $semester);
        $applicationsInSemester = [];
        if ($admissionPeriod !== null) {
            $applicationsInSemester = $this->applicationRepo
                ->findByAdmissionPeriod($admissionPeriod);
            $appData = $this->admissionStatistics->generateCumulativeGraphDataFromApplicationsInAdmissionPeriod($applicationsInSemester, $admissionPeriod);
        }

        return $this->render('widgets/application_graph_widget.html.twig', [
            'appData' => $appData,
            'semester' => $semester,
        ]);
    }

    /**
     * @return Response|null
     */
    public function availableSurveysAction(Request $request)
    {
        $semester = $this->getSemesterOrThrow404($request);
        $surveys = [];
        if ($semester !== null) {
            $surveys = $this->surveyRepo
                ->findAllNotTakenByUserAndSemester($this->getUser(), $semester);
        }

        return $this->render('widgets/available_surveys_widget.html.twig', [
            'availableSurveys' => $surveys,
        ]);
    }

    public function changelogAction()
    {
        $changeLogItems = $this->changeLogItemRepo->findAllOrderedByDate();
        $changeLogItems = array_reverse($changeLogItems);

        return $this->render('widgets/changelog_widget.html.twig', [
            'changeLogItems' => array_slice($changeLogItems, 0, 5),
        ]);
    }

    public function feedbackAction(Request $request)
    {
        $feedback = new Feedback();
        $form = $this->createForm(FeedbackType::class, $feedback);
        $form->handleRequest($request);

        return $this->render('widgets/feedback_widget.html.twig', [
            'title' => 'Feedback',
            'form' => $form->createView(),
        ]);
    }
}
