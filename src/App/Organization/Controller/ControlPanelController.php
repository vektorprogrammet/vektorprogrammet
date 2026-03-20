<?php

declare(strict_types=1);

namespace App\Organization\Controller;

use App\Support\Controller\BaseController;
use App\Admission\Infrastructure\Repository\AdmissionPeriodRepository;
use App\Organization\Infrastructure\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Scheduling\Infrastructure\SbsData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ControlPanelController extends BaseController
{
    public function __construct(
        private readonly AdmissionPeriodRepository $admissionPeriodRepo,
        private readonly SbsData $sbsData,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    #[Route('/kontrollpanel', name: 'control_panel', methods: ['GET'])]
    public function showAction(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);

        $admissionPeriod = $this->admissionPeriodRepo
            ->findOneByDepartmentAndSemester($department, $semester);

        // Return the view to be rendered
        return $this->render('control_panel/index.html.twig', [
            'admissionPeriod' => $admissionPeriod,
        ]);
    }

    public function showSBSAction()
    {
        /** @var \App\Identity\Infrastructure\Entity\User $user */
        $user = $this->getUser();
        $currentAdmissionPeriod = $user->getDepartment()->getCurrentAdmissionPeriod();

        if ($currentAdmissionPeriod !== null) {
            $this->sbsData->setAdmissionPeriod($currentAdmissionPeriod);
        }

        // Return the view to be rendered
        return $this->render('control_panel/sbs.html.twig', [
            'data' => $this->sbsData,
        ]);
    }
}
