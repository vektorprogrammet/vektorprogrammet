<?php

declare(strict_types=1);

namespace App\Organization\Controller;

use App\Identity\Infrastructure\Entity\User;
use App\Support\Controller\BaseController;
use App\Organization\Infrastructure\Entity\FieldOfStudy;
use App\Organization\Infrastructure\Repository\DepartmentRepository;
use App\Organization\Infrastructure\Repository\FieldOfStudyRepository;
use App\Shared\Repository\SemesterRepository;
use App\Organization\Form\FieldOfStudyType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class FieldOfStudyController extends BaseController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly FieldOfStudyRepository $fieldOfStudyRepo,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    #[Route('/kontrollpanel/linjer', name: 'show_field_of_studies', methods: ['GET'])]
    public function showAction()
    {
        /** @var User $user */
        $user = $this->getUser();
        $department = $user->getFieldOfStudy()->getDepartment();
        $fieldOfStudies = $this->fieldOfStudyRepo->findByDepartment($department);

        return $this->render('field_of_study/show_all.html.twig', [
            'fieldOfStudies' => $fieldOfStudies,
            'department' => $department,
        ]);
    }

    #[Route('/kontrollpanel/linje', name: 'create_field_of_study', methods: ['GET', 'POST'])]
    #[Route('/kontrollpanel/linje/{id}', name: 'edit_field_of_study', methods: ['GET', 'POST'])]
    public function editAction(Request $request, ?FieldOfStudy $fieldOfStudy = null)
    {
        $isEdit = true;
        if ($fieldOfStudy === null) {
            $fieldOfStudy = new FieldOfStudy();
            $isEdit = false;
        } else {
            // Check if user is trying to edit FOS from department other than his own
            /** @var User $user */
            $user = $this->getUser();
            if ($fieldOfStudy->getDepartment() !== $user->getFieldOfStudy()->getDepartment()) {
                throw new AccessDeniedException();
            }
        }
        $form = $this->createForm(FieldOfStudyType::class, $fieldOfStudy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();
            $fieldOfStudy->setDepartment($user->getFieldOfStudy()->getDepartment());
            $this->em->persist($fieldOfStudy);
            $this->em->flush();

            return $this->redirectToRoute('show_field_of_studies');
        }

        return $this->render('field_of_study/form.html.twig', [
            'form' => $form->createView(),
            'isEdit' => $isEdit,
            'fieldOfStudy' => $fieldOfStudy,
        ]);
    }
}
