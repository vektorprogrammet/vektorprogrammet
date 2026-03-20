<?php

namespace App\Operations\Controller;

use App\Support\Controller\BaseController;
use App\Identity\Infrastructure\Entity\User;
use App\Operations\Infrastructure\Entity\AssistantHistory;
use App\Organization\Infrastructure\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Operations\Form\CreateAssistantHistoryType;
use App\Identity\Domain\Roles;
use App\Support\Infrastructure\LogService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class AssistantHistoryController extends BaseController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly LogService $logService,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    #[Route('/kontrollpanel/deltakerhistorikk/slett/{id}', name: 'assistant_history_delete', methods: ['POST'])]
    public function deleteAction(AssistantHistory $assistantHistory)
    {
        $currentUser = $this->getUser();
        assert($currentUser instanceof User);
        if (!$this->isGranted(Roles::ADMIN) && $assistantHistory->getUser()->getDepartment() !== $currentUser->getDepartment()) {
            $this->createAccessDeniedException();
        }

        $this->em->remove($assistantHistory);
        $this->em->flush();

        $this->logService->info(
            "{$this->getUser()} deleted {$assistantHistory->getUser()}'s assistant history on ".
            "{$assistantHistory->getSchool()->getName()} {$assistantHistory->getSemester()->getName()}"
        );

        return $this->redirectToRoute('participanthistory_show');
    }

    #[Route('/kontrollpanel/deltakerhistorikk/rediger/{id}', name: 'assistant_history_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function editAction(Request $request, AssistantHistory $assistantHistory)
    {
        $department = $assistantHistory->getUser()->getDepartment();
        $form = $this->createForm(CreateAssistantHistoryType::class, $assistantHistory, [
            'department' => $department,
        ]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->em->persist($assistantHistory);
            $this->em->flush();

            return $this->redirectToRoute('participanthistory_show');
        }

        return $this->render('participant_history/participant_history_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
