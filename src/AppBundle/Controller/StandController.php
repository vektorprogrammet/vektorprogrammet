<?php

namespace AppBundle\Controller;

use AppBundle\Repository\Contract\AdmissionPeriodRepositoryInterface;
use AppBundle\Repository\Contract\AdmissionSubscriberRepositoryInterface;
use AppBundle\Repository\Contract\ApplicationRepositoryInterface;
use AppBundle\Service\Contract\AdmissionStatisticsInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StandController extends BaseController
{
    private $admissionStatistics;
    private $applicationRepository;
    private $admissionPeriodRepository;
    private $admissionSubscriberRepository;

    /**
     * @param AdmissionStatisticsInterface $admissionStatistics
     * @param ApplicationRepositoryInterface $applicationRepository
     * @param AdmissionPeriodRepositoryInterface $admissionPeriodRepository
     * @param AdmissionSubscriberRepositoryInterface $admissionSubscriberRepository
     */
    public function __construct(
        AdmissionStatisticsInterface $admissionStatistics,
        ApplicationRepositoryInterface $applicationRepository,
        AdmissionPeriodRepositoryInterface $admissionPeriodRepository,
        AdmissionSubscriberRepositoryInterface $admissionSubscriberRepository
    ) {
        $this->admissionStatistics = $admissionStatistics;
        $this->applicationRepository = $applicationRepository;
        $this->admissionPeriodRepository = $admissionPeriodRepository;
        $this->admissionSubscriberRepository = $admissionSubscriberRepository;
    }

    /**
     * @Route("/kontrollpanel/stand", name="stand")
     *
     * @param Request $request
     * @return Response
     * @throws NonUniqueResultException
     */
    public function indexAction(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);

        $subscribers = $this->admissionSubscriberRepository->findFromWebByDepartment($department);
        $subscribersInDepartmentAndSemester = $this->admissionSubscriberRepository
            ->findFromWebByDepartmentAndSemester($department, $semester);
        $subData = $this->admissionStatistics->generateGraphDataFromSubscribersInSemester($subscribersInDepartmentAndSemester, $semester);

        $applications = $this->applicationRepository->findByDepartment($department);
        $admissionPeriod = $this->admissionPeriodRepository->findOneByDepartmentAndSemester($department, $semester);
        $applicationsInSemester = [];
        $appData = null;
        if ($admissionPeriod !== null) {
            $applicationsInSemester = $this->applicationRepository->findByAdmissionPeriod($admissionPeriod);
            $appData = $this->admissionStatistics->generateGraphDataFromApplicationsInAdmissionPeriod($applicationsInSemester, $admissionPeriod);
        }


        return $this->render('stand_admin/stand.html.twig', [
            'department' => $department,
            'semester' => $semester,
            'subscribers' => $subscribers,
            'subscribers_in_semester' => $subscribersInDepartmentAndSemester,
            'subData' => $subData,
            'applications' => $applications,
            'applications_in_semester' => $applicationsInSemester,
            'appData' => $appData,
        ]);
    }
}
