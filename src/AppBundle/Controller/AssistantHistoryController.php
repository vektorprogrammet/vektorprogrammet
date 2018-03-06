<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AssistantHistory;
use AppBundle\Event\AssistantHistoryEditedEvent;
use AppBundle\Event\AssistantHistoryEventEdited;
use AppBundle\Role\Roles;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\Type\CreateAssistantHistoryType;
use Symfony\Component\HttpFoundation\Request;

class AssistantHistoryController extends Controller
{
    public function deleteAction(AssistantHistory $assistantHistory)
    {
        if (!$this->isGranted(Roles::ADMIN) && $assistantHistory->getUser()->getDepartment() !== $this->getUser()->getDepartment()) {
            $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($assistantHistory);
        $em->flush();

        $this->get('app.logger')->info(
            "{$this->getUser()} deleted {$assistantHistory->getUser()}'s assistant history on ".
            "{$assistantHistory->getSchool()->getName()} {$assistantHistory->getSemester()->getName()}"
        );

        return $this->redirectToRoute('participanthistory_show');
    }

    public function editAction(Request $request, AssistantHistory $assistantHistory)
    {
        $em = $this->getDoctrine()->getManager();

        $department = $this->getUser()->getDepartment();
        $form = $this->createForm(new CreateAssistantHistoryType($department), $assistantHistory);
        $form->handleRequest($request);

        if ($form -> isValid()) {
            $em->persist($assistantHistory);
            $em->flush();
            $this->get('event_dispatcher')->dispatch(AssistantHistoryEditedEvent::EDITED, new AssistantHistoryEditedEvent($assistantHistory));
            return $this->redirectToRoute('participanthistory_show');
        }
        return $this->render("participant_history/participant_history_edit.html.twig", array(
            "form"=>$form->createView()
        ));
    }
}
