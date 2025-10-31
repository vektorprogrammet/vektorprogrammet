<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Repository\Contract\AdmissionPeriodRepositoryInterface;
use AppBundle\Service\Contract\SbsDataInterface;
use Symfony\Component\HttpFoundation\Request;

class ControlPanelController extends BaseController
{
    private $admissionPeriodRepository;
    private $sbsData;

    /**
     * @param AdmissionPeriodRepositoryInterface $admissionPeriodRepository
     * @param SbsDataInterface $sbsData
     */
    public function __construct(
        AdmissionPeriodRepositoryInterface $admissionPeriodRepository,
        SbsDataInterface $sbsData
    ) {
        $this->admissionPeriodRepository = $admissionPeriodRepository;
        $this->sbsData = $sbsData;
    }

    /**
     *
     * @param Request $request
     */
    public function showAction(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);

        $admissionPeriod = $this->admissionPeriodRepository->findOneByDepartmentAndSemester($department, $semester);

        // Return the view to be rendered
        return $this->render('control_panel/index.html.twig', array(
            'admissionPeriod' => $admissionPeriod,
        ));
    }

    public function showSBSAction()
    {
        $currentAdmissionPeriod = $this->getUser()->getDepartment()->getCurrentAdmissionPeriod();

        if ($currentAdmissionPeriod) {
            $this->sbsData->setAdmissionPeriod($currentAdmissionPeriod);
        }

        // Return the view to be rendered
        return $this->render('control_panel/sbs.html.twig', array(
            'data' => $this->sbsData,
        ));
    }
}
