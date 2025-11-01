<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Entity\AdmissionSubscriber;
use AppBundle\Entity\Application;
use AppBundle\Repository\Contract\AdmissionPeriodRepositoryInterface;
use AppBundle\Repository\Contract\ApplicationRepositoryInterface;
use AppBundle\Service\Contract\AdmissionStatisticsInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StandController extends BaseController
{
    private $admissionStatistics;
    private $admissionPeriodRepository;
    private $applicationRepository;
    private $entityManager;

    /**
     * @param AdmissionStatisticsInterface $admissionStatistics
     * @param AdmissionPeriodRepositoryInterface $admissionPeriodRepository
     * @param ApplicationRepositoryInterface $applicationRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        AdmissionStatisticsInterface $admissionStatistics,
        AdmissionPeriodRepositoryInterface $admissionPeriodRepository,
        ApplicationRepositoryInterface $applicationRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->admissionStatistics = $admissionStatistics;
        $this->admissionPeriodRepository = $admissionPeriodRepository;
        $this->applicationRepository = $applicationRepository;
        $this->entityManager = $entityManager;
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

        $subscribers = $this->entityManager->getRepository(AdmissionSubscriber::class)->findFromWebByDepartment($department);
        $subscribersInDepartmentAndSemester = $this->entityManager->getRepository(AdmissionSubscriber::class)
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
