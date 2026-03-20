<?php

namespace App\Admission\Controller;

use App\Support\Controller\BaseController;
use App\Admission\Infrastructure\Entity\Application;
use App\Admission\Infrastructure\Repository\AdmissionPeriodRepository;
use App\Admission\Infrastructure\Repository\ApplicationRepository;
use App\Organization\Infrastructure\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Admission\Form\ModifySubstituteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

/**
 * SubstituteController is the controller responsible for substitute assistants,
 * such as showing, modifying and deleting substitutes.
 */
class SubstituteController extends BaseController
{
    public function __construct(
        private readonly AdmissionPeriodRepository $admissionPeriodRepo,
        private readonly ApplicationRepository $applicationRepo,
        private readonly EntityManagerInterface $em,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    /**
     * @return Response|null
     */
    #[Route('/kontrollpanel/vikar', name: 'substitute_show', methods: ['GET'])]
    public function showAction(Request $request)
    {
        // No department specified, get the user's department and call showBySemester with
        // either current or latest semester for that department
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);

        $admissionPeriod = $this->admissionPeriodRepo
            ->findOneByDepartmentAndSemester($department, $semester);

        $substitutes = null;
        if ($admissionPeriod !== null) {
            $substitutes = $this->applicationRepo
                ->findSubstitutesByAdmissionPeriod($admissionPeriod);
        }

        return $this->render('substitute/index.html.twig', [
            'substitutes' => $substitutes,
            'semester' => $semester,
            'department' => $department,
        ]);
    }

    #[Route('/kontrollpanel/vikar/rediger/{id}', name: 'substitute_modify', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function showModifyFormAction(Request $request, Application $application)
    {
        // Only substitutes should be modified with this form
        if (!$application->isSubstitute()) {
            throw new BadRequestHttpException();
        }

        $department = $application->getUser()->getDepartment();

        $form = $this->createForm(ModifySubstituteType::class, $application, [
            'department' => $department,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($application);
            $this->em->flush();

            // Need some form of redirect. Will cause wrong database entries if the form is rendered again
            // after a valid submit, without remaking the form with up to date question objects from the database.
            return $this->redirect($this->generateUrl('substitute_show', [
                'semester' => $application->getSemester()->getId(),
                'department' => $department->getId(),
            ]));
        }

        return $this->render('substitute/modify_substitute.twig', [
            'application' => $application,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/kontrollpanel/vikar/slett/{id}', name: 'substitute_delete', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function deleteSubstituteByIdAction(Application $application)
    {
        $application->setSubstitute(false);

        $this->em->persist($application);
        $this->em->flush();

        // Redirect to substitute page, set semester to that of the deleted substitute
        return $this->redirectToRoute('substitute_show', [
            'semester' => $application->getSemester()->getId(),
            'department' => $application->getAdmissionPeriod()->getDepartment()->getid(),
        ]);
    }

    #[Route('/kontrollpanel/vikar/opprett/{id}', name: 'substitute_create_from_application', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function createSubstituteFromApplicationAction(Application $application)
    {
        if ($application->isSubstitute()) {
            // User is already substitute
            throw new BadRequestHttpException();
        }
        $application->setSubstitute(true);

        $this->em->persist($application);
        $this->em->flush();

        // Redirect to substitute page, set semester to that of the newly added substitute
        return $this->redirectToRoute('substitute_show', [
            'semester' => $application->getSemester()->getId(),
            'department' => $application->getAdmissionPeriod()->getDepartment()->getId(),
        ]);
    }
}
