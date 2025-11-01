<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Repository\Contract\AdmissionPeriodRepositoryInterface;
use AppBundle\Service\Contract\ApplicationDataInterface;
use AppBundle\Service\Contract\AssistantHistoryDataInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplicationStatisticsController extends BaseController
{
    private $admissionPeriodRepository;
    private $assistantHistoryData;
    private $applicationData;

    /**
     * @param AdmissionPeriodRepositoryInterface $admissionPeriodRepository
     * @param AssistantHistoryDataInterface $assistantHistoryData
     * @param ApplicationDataInterface $applicationData
     */
    public function __construct(
        AdmissionPeriodRepositoryInterface $admissionPeriodRepository,
        AssistantHistoryDataInterface $assistantHistoryData,
        ApplicationDataInterface $applicationData
    ) {
        $this->admissionPeriodRepository = $admissionPeriodRepository;
        $this->assistantHistoryData = $assistantHistoryData;
        $this->applicationData = $applicationData;
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
        $admissionPeriod = $this->admissionPeriodRepository->findOneByDepartmentAndSemester($department, $semester);

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
