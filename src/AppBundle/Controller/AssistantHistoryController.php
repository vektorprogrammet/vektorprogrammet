<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AssistantHistory;
use AppBundle\Role\Roles;
use AppBundle\Form\Type\CreateAssistantHistoryType;
use AppBundle\Service\LogService;
use Symfony\Component\HttpFoundation\Request;

class AssistantHistoryController extends BaseController
{
    public function deleteAction(AssistantHistory $assistantHistory)
    {
        if (!$this->isGranted(Roles::ADMIN) && $assistantHistory->getUser()->getDepartment() !== $this->getUser()->getDepartment()) {
            $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($assistantHistory);
        $em->flush();

        $this->get(LogService::class)->info(
            "{$this->getUser()} deleted {$assistantHistory->getUser()}'s assistant history on ".
            "{$assistantHistory->getSchool()->getName()} {$assistantHistory->getSemester()->getName()}"
        );

        return $this->redirectToRoute('participanthistory_show');
    }

    public function editAction(Request $request, AssistantHistory $assistantHistory)
    {
        $em = $this->getDoctrine()->getManager();

        $department = $assistantHistory->getUser()->getDepartment();
        $form = $this->createForm(CreateAssistantHistoryType::class, $assistantHistory, [
            'department' => $department
        ]);
        $form->handleRequest($request);

        if ($form -> isValid()) {
            $em->persist($assistantHistory);
            $em->flush();
            return $this->redirectToRoute('participanthistory_show');
        }
        return $this->render("participant_history/participant_history_edit.html.twig", array(
            "form"=>$form->createView()
        ));
    }
}
