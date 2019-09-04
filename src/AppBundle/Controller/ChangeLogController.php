<?php


namespace AppBundle\Controller;

use AppBundle\Entity\ChangeLogItem;
use AppBundle\Form\Type\ChangeLogType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ChangeLogController extends BaseController
{
    public function createChangeLogAction(Request $request)
    {
        $changelogitem = new ChangeLogItem();
        $form = $this->createForm(ChangeLogType::class, $changelogitem);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($changelogitem);
            $em->flush();

            return $this->redirect($this->generateUrl('control_panel'));
        }

        return $this->render('changelog/changelog_create.html.twig', array(
            'form' => $form->createView(),
            'changelog' => $changelogitem,
        ));
    }

    public function editChangeLogAction(Request $request, ChangeLogItem $changeLogItem)
    {
        $form = $this->createForm(ChangeLogType::class, $changeLogItem);
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($changeLogItem);
            $em->flush();

            return $this->redirect($this->generateUrl('control_panel'));
        }

        return $this->render('changelog/changelog_create.html.twig', array(
            'form' => $form->createView(),
            'changelog' => $changeLogItem,
        ));
    }

    public function deleteChangeLogAction(Request $request, ChangeLogItem $changeLogItem)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($changeLogItem);
        $em->flush();

        $this->addFlash("success", "\"".$changeLogItem->getTitle()."\" ble slettet");

        return $this->redirect($this->generateUrl('control_panel'));
    }
}
