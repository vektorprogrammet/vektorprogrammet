<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Entity\Application;
use AppBundle\Entity\ChangeLogItem;
use AppBundle\Entity\Receipt;
use AppBundle\Entity\Feedback;
use AppBundle\Entity\Survey;
use AppBundle\Entity\User;
use AppBundle\Form\Type\FeedbackType;
use AppBundle\Service\AdmissionStatistics;
use AppBundle\Service\Sorter;
use AppBundle\Service\TodoListService;
use AppBundle\Utils\ReceiptStatistics;
use Symfony\Component\HttpFoundation\Request;

class WidgetController extends BaseController
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    public function interviewsAction(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);
        $admissionPeriod = $this->getDoctrine()->getRepository(AdmissionPeriod::class)
            ->findOneByDepartmentAndSemester($department, $semester);
        $applicationsAssignedToUser = [];

        if ($admissionPeriod !== null) {
            $applicationRepo = $this->getDoctrine()->getRepository(Application::class);
            $applicationsAssignedToUser = $applicationRepo->findAssignedByUserAndAdmissionPeriod($this->getUser(), $admissionPeriod);
        }

        return $this->render('widgets/interviews_widget.html.twig', ['applications' => $applicationsAssignedToUser]);
    }

    public function receiptsAction()
    {
        $usersWithReceipts = $this->getDoctrine()->getRepository(User::class)->findAllUsersWithReceipts();
        $sorter = $this->container->get(Sorter::class);

        $sorter->sortUsersByReceiptSubmitTime($usersWithReceipts);
        $sorter->sortUsersByReceiptStatus($usersWithReceipts);

        $pendingReceipts = $this->getDoctrine()->getRepository(Receipt::class)->findByStatus(Receipt::STATUS_PENDING);
        $pendingReceiptStatistics = new ReceiptStatistics($pendingReceipts);

        $hasReceipts = !empty($pendingReceipts);

        return $this->render('widgets/receipts_widget.html.twig', [
            'users_with_receipts' => $usersWithReceipts,
            'statistics' => $pendingReceiptStatistics,
            'has_receipts' => $hasReceipts,
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    public function applicationGraphAction(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);

        $admissionStatistics = $this->get(AdmissionStatistics::class);

        $admissionPeriod = $this->getDoctrine()->getRepository(AdmissionPeriod::class)
            ->findOneByDepartmentAndSemester($department, $semester);
        $applicationsInSemester = [];
        if ($admissionPeriod !== null) {
            $applicationsInSemester = $this->getDoctrine()
                ->getRepository(Application::class)
                ->findByAdmissionPeriod($admissionPeriod);
        }
        $appData = $admissionStatistics->generateCumulativeGraphDataFromApplicationsInAdmissionPeriod($applicationsInSemester, $admissionPeriod);

        return $this->render('widgets/application_graph_widget.html.twig', [
            'appData' => $appData,
            'semester' => $semester,
        ]);
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    public function availableSurveysAction(Request $request)
    {
        $semester = $this->getSemesterOrThrow404($request);

        $surveys = $this->getDoctrine()
            ->getRepository(Survey::class)
            ->findAllNotTakenByUserAndSemester($this->getUser(), $semester);


        return $this->render('widgets/available_surveys_widget.html.twig', [
            'availableSurveys' => $surveys,
        ]);
    }

    public function changelogAction()
    {
        $changeLogItems = $this->getDoctrine()->getRepository(ChangeLogItem::class)->findAllOrderedByDate();
        $changeLogItems = array_reverse($changeLogItems);

        return $this->render('widgets/changelog_widget.html.twig', [
            'changeLogItems' => array_slice($changeLogItems, 0, 5)
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    public function todoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $currentSemester = $this->getSemesterOrThrow404($request);
        $department = $this->getDepartmentOrThrow404($request);

        $todoService = new TodoListService($em);

        // Get sorted
        $todoItems        = $todoService->getOrderedList($department, $currentSemester);
        // Don't show completed
        $incompletedItems = $todoService->getIncompletedTodoItems($todoItems, $currentSemester, $department);

        return $this->render('widgets/todo_widget.html.twig', array(
            'todo_items' => $incompletedItems,
            'semester' => $currentSemester,
            'department' => $department
        ));
    }

    public function feedbackAction(Request $request)
    {
        $feedback = new Feedback;
        $form = $this->createForm(FeedBackType::class, $feedback);
        $form->handleRequest($request);

        return $this->render('widgets/feedback_widget.html.twig', array(
            'title' => 'Feedback',
            'form' => $form->createView()
        ));
    }
}
