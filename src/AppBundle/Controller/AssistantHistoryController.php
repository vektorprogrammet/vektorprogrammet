<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AssistantHistory;
use AppBundle\Form\Type\CreateAssistantHistoryType;
use AppBundle\Role\Roles;
use AppBundle\Service\Contract\LogServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class AssistantHistoryController extends BaseController
{
    private $entityManager;
    private $logService;

    /**
     * @param EntityManagerInterface $entityManager
     * @param LogServiceInterface $logService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        LogServiceInterface $logService
    ) {
        $this->entityManager = $entityManager;
        $this->logService = $logService;
    }
    public function deleteAction(AssistantHistory $assistantHistory)
    {
        if (!$this->isGranted(Roles::ADMIN) && $assistantHistory->getUser()->getDepartment() !== $this->getUser()->getDepartment()) {
            $this->createAccessDeniedException();
        }

        $this->entityManager->remove($assistantHistory);
        $this->entityManager->flush();

        $this->logService->info(
            "{$this->getUser()} deleted {$assistantHistory->getUser()}'s assistant history on ".
            "{$assistantHistory->getSchool()->getName()} {$assistantHistory->getSemester()->getName()}"
        );

        return $this->redirectToRoute('participanthistory_show');
    }

    public function editAction(Request $request, AssistantHistory $assistantHistory)
    {
        $department = $assistantHistory->getUser()->getDepartment();
        $form = $this->createForm(CreateAssistantHistoryType::class, $assistantHistory, [
            'department' => $department
        ]);
        $form->handleRequest($request);

        if ($form -> isValid()) {
            $this->entityManager->persist($assistantHistory);
            $this->entityManager->flush();
            return $this->redirectToRoute('participanthistory_show');
        }
        return $this->render("participant_history/participant_history_edit.html.twig", array(
            "form"=>$form->createView()
        ));
    }
}
