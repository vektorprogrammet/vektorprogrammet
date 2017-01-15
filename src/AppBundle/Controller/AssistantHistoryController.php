<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AssistantHistory;
use AppBundle\Service\RoleManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AssistantHistoryController extends Controller
{
    public function deleteAction(AssistantHistory $assistantHistory)
    {
        if (!$this->isGranted(RoleManager::ROLE_ADMIN) && $assistantHistory->getUser()->getDepartment() !== $this->getUser()->getDepartment()) {
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
}
