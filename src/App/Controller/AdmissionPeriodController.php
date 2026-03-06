<?php

namespace App\Controller;

use App\Entity\AdmissionPeriod;
use App\Entity\Department;
use App\Entity\Repository\AdmissionPeriodRepository;
use App\Entity\Repository\DepartmentRepository;
use App\Entity\Repository\SemesterRepository;
use App\Form\Type\CreateAdmissionPeriodType;
use App\Form\Type\EditAdmissionPeriodType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class AdmissionPeriodController extends BaseController
{
    public function __construct(
        private readonly AdmissionPeriodRepository $admissionPeriodRepo,
        private readonly EntityManagerInterface $em,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    #[Route('/kontrollpanel/opptaksperiode', name: 'admission_period_admin_show', methods: ['GET'])]
    public function showAction()
    {
        // Finds the departmentId for the current logged in user
        $department = $this->getUser()->getDepartment();

        return $this->showByDepartmentAction($department);
    }

    #[Route('/kontrollpanel/opptaksperiode/{id}', name: 'admission_period_admin_show_by_department', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function showByDepartmentAction(Department $department)
    {
        $admissionPeriods = $this->admissionPeriodRepo
            ->findByDepartmentOrderedByTime($department);

        // Renders the view with the variables
        return $this->render('admission_period_admin/index.html.twig', [
            'admissionPeriods' => $admissionPeriods,
            'departmentName' => $department->getShortName(),
            'department' => $department,
        ]);
    }

    #[Route('/kontrollpanel/opptaksperiode/opprett/{id}', name: 'admission_period_create', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function createAdmissionPeriodAction(Request $request, Department $department)
    {
        $admissionPeriod = new AdmissionPeriod();
        $admissionPeriods = $department->getAdmissionPeriods()->toArray();
        $form = $this->createForm(CreateAdmissionPeriodType::class, $admissionPeriod, [
            'admissionPeriods' => $admissionPeriods,
        ]);

        $form->handleRequest($request);

        $exists = $department->getAdmissionPeriods()->exists(fn ($key, $value) => $value->getSemester() === $admissionPeriod->getSemester());

        if ($exists) {
            $this->addFlash('warning', 'Opptaksperioden '.$admissionPeriod->getSemester().' finnes allerede.');
        }
        if ($form->isSubmitted() && $form->isValid() && !$exists) {
            $admissionPeriod->setDepartment($department);

            $this->em->persist($admissionPeriod);
            $this->em->flush();

            return $this->redirectToRoute('admission_period_admin_show_by_department', ['id' => $department->getId()]);
        }

        // Render the view
        return $this->render('admission_period_admin/create_admission_period.html.twig', [
            'department' => $department,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/kontrollpanel/opptaksperiode/update/{id}', name: 'admission_period_update', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function updateAdmissionPeriodAction(Request $request, AdmissionPeriod $admissionPeriod)
    {
        $form = $this->createForm(EditAdmissionPeriodType::class, $admissionPeriod);

        // Handle the form
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($admissionPeriod);
            $this->em->flush();

            return $this->redirectToRoute('admission_period_admin_show_by_department', ['id' => $admissionPeriod->getDepartment()->getId()]);
        }

        return $this->render('admission_period_admin/edit_admission_period.html.twig', [
            'form' => $form->createView(),
            'semesterName' => $admissionPeriod->getSemester()->getName(),
            'department' => $admissionPeriod->getDepartment(),
        ]);
    }

    #[Route('/kontrollpanel/opptaksperiode/slett/{id}', name: 'admission_period_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteAction(AdmissionPeriod $admissionPeriod)
    {
        $infoMeeting = $admissionPeriod->getInfoMeeting();
        if ($infoMeeting) {
            $this->em->remove($infoMeeting);
        }
        $this->em->remove($admissionPeriod);
        $this->em->flush();

        return $this->redirectToRoute('admission_period_admin_show_by_department', ['id' => $admissionPeriod->getDepartment()->getId()]);
    }
}
