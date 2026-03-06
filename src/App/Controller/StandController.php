<?php

namespace App\Controller;

use App\Entity\Repository\AdmissionPeriodRepository;
use App\Entity\Repository\AdmissionSubscriberRepository;
use App\Entity\Repository\ApplicationRepository;
use App\Entity\Repository\DepartmentRepository;
use App\Entity\Repository\SemesterRepository;
use App\Service\AdmissionStatistics;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class StandController extends BaseController
{
    public function __construct(
        private readonly AdmissionStatistics $admissionStatistics,
        private readonly AdmissionSubscriberRepository $admissionSubscriberRepo,
        private readonly ApplicationRepository $applicationRepo,
        private readonly AdmissionPeriodRepository $admissionPeriodRepo,
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
    #[Route('/kontrollpanel/stand', name: 'stand')]
    public function indexAction(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);

        $subscribers = $this->admissionSubscriberRepo->findFromWebByDepartment($department);
        $subscribersInDepartmentAndSemester = $this->admissionSubscriberRepo
            ->findFromWebByDepartmentAndSemester($department, $semester);
        $subData = $this->admissionStatistics->generateGraphDataFromSubscribersInSemester($subscribersInDepartmentAndSemester, $semester);

        $applications = $this->applicationRepo->findByDepartment($department);
        $admissionPeriod = $this->admissionPeriodRepo
            ->findOneByDepartmentAndSemester($department, $semester);
        $applicationsInSemester = [];
        $appData = null;
        if ($admissionPeriod !== null) {
            $applicationsInSemester = $this->applicationRepo
                ->findByAdmissionPeriod($admissionPeriod);
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
