<?php

namespace App\Controller;

use App\Entity\AdmissionPeriod;
use App\Entity\Repository\AdmissionPeriodRepository;
use App\Entity\Repository\DepartmentRepository;
use App\Entity\Repository\SemesterRepository;
use App\Service\ApplicationData;
use App\Service\AssistantHistoryData;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplicationStatisticsController extends BaseController
{
    public function __construct(
        private AdmissionPeriodRepository $admissionPeriodRepo,
        private AssistantHistoryData $assistantHistoryData,
        private ApplicationData $applicationData,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws NonUniqueResultException
     */
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

        return $this->render('statistics/statistics.html.twig', array(
            'applicationData' => $this->applicationData,
            'assistantHistoryData' => $this->assistantHistoryData,
            'semester' => $semester,
            'department' => $department,
        ));
    }
}
