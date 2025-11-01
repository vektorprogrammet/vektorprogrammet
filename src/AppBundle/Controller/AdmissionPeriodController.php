<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Entity\Department;
use AppBundle\Form\Type\CreateAdmissionPeriodType;
use AppBundle\Form\Type\EditAdmissionPeriodType;
use AppBundle\Repository\Contract\AdmissionPeriodRepositoryInterface;
use AppBundle\Service\Contract\AdmissionPeriodManagementServiceInterface;
use Symfony\Component\HttpFoundation\Request;

class AdmissionPeriodController extends BaseController
{
    private $admissionPeriodRepository;
    private $admissionPeriodManagementService;

    /**
     * @param AdmissionPeriodRepositoryInterface $admissionPeriodRepository
     * @param AdmissionPeriodManagementServiceInterface $admissionPeriodManagementService
     */
    public function __construct(
        AdmissionPeriodRepositoryInterface $admissionPeriodRepository,
        AdmissionPeriodManagementServiceInterface $admissionPeriodManagementService
    ) {
        $this->admissionPeriodRepository = $admissionPeriodRepository;
        $this->admissionPeriodManagementService = $admissionPeriodManagementService;
    }
    public function showAction()
    {
        // Finds the departmentId for the current logged in user
        $department = $this->getUser()->getDepartment();

        return $this->showByDepartmentAction($department);
    }

    public function showByDepartmentAction(Department $department)
    {
        $admissionPeriods = $this->admissionPeriodRepository->findByDepartmentOrderedByTime($department);


        // Renders the view with the variables
        return $this->render('admission_period_admin/index.html.twig', array(
            'admissionPeriods' => $admissionPeriods,
            'departmentName' => $department->getShortName(),
            'department' => $department
        ));
    }

    public function createAdmissionPeriodAction(Request $request, Department $department)
    {
        $admissionPeriod = new AdmissionPeriod();
        $admissionPeriods = $department->getAdmissionPeriods()->toArray();
        $form = $this->createForm(CreateAdmissionPeriodType::class, $admissionPeriod, [
            'admissionPeriods' => $admissionPeriods
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->admissionPeriodManagementService->createAdmissionPeriod($admissionPeriod, $department);

            if ($result['exists']) {
                $this->addFlash('warning', 'Opptaksperioden ' . $admissionPeriod->getSemester() . ' finnes allerede.');
            } else {
                return $this->redirectToRoute('admission_period_admin_show_by_department', array('id' => $department->getId()));
            }
        }

        // Render the view
        return $this->render('admission_period_admin/create_admission_period.html.twig', array(
            'department' => $department,
            'form' => $form->createView(),
        ));
    }

    public function updateAdmissionPeriodAction(Request $request, AdmissionPeriod $admissionPeriod)
    {
        $form = $this->createForm(EditAdmissionPeriodType::class, $admissionPeriod);

        // Handle the form
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->admissionPeriodManagementService->updateAdmissionPeriod($admissionPeriod);

            return $this->redirectToRoute('admission_period_admin_show_by_department', array('id' => $admissionPeriod->getDepartment()->getId()));
        }

        return $this->render('admission_period_admin/edit_admission_period.html.twig', array(
            'form' => $form->createView(),
            'semesterName' => $admissionPeriod->getSemester()->getName(),
            'department' => $admissionPeriod->getDepartment(),
        ));
    }

    public function deleteAction(AdmissionPeriod $admissionPeriod)
    {
        $this->admissionPeriodManagementService->deleteAdmissionPeriod($admissionPeriod);

        return $this->redirectToRoute('admission_period_admin_show_by_department', ['id' => $admissionPeriod->getDepartment()->getId()]);
    }
}
