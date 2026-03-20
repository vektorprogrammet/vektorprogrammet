<?php

namespace App\Admission\Controller;

use App\Support\Controller\BaseController;
use App\Admission\Infrastructure\Repository\AdmissionPeriodRepository;
use App\Organization\Infrastructure\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Admission\Infrastructure\ApplicationData;
use App\Operations\Infrastructure\AssistantHistoryData;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ApplicationStatisticsController extends BaseController
{
    public function __construct(
        private readonly AdmissionPeriodRepository $admissionPeriodRepo,
        private readonly AssistantHistoryData $assistantHistoryData,
        private readonly ApplicationData $applicationData,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    /**
     * @return Response
     *
     * @throws NonUniqueResultException
     */
    #[Route('/kontrollpanel/statistikk/opptak', name: 'statistics_application_show', methods: ['GET'])]
    public function showAction(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);
        $admissionPeriod = $this->admissionPeriodRepo
            ->findOneByDepartmentAndSemester($department, $semester);

        $this->assistantHistoryData->setSemester($semester)->setDepartment($department);

        if ($admissionPeriod !== null) {
            $this->applicationData->setAdmissionPeriod($admissionPeriod);
        }

        return $this->render('statistics/statistics.html.twig', [
            'applicationData' => $this->applicationData,
            'assistantHistoryData' => $this->assistantHistoryData,
            'semester' => $semester,
            'department' => $department,
        ]);
    }
}
