<?php


namespace AppBundle\Controller;

use AppBundle\Entity\ChangeLogItem;
use AppBundle\Form\Type\ChangeLogType;
use Symfony\Component\HttpFoundation\Request;

class ChangeLogController extends BaseController
{
    public function createChangeLogAction(Request $request)
    {
        $changeLogItem = new ChangeLogItem();
        $form = $this->createForm(ChangeLogType::class, $changeLogItem);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($changeLogItem);
            $em->flush();

            return $this->redirect($this->generateUrl('changelog_show_all'));
        }

        return $this->render('changelog/changelog_create.html.twig', array(
            'form' => $form->createView(),
            'changelog' => $changeLogItem,
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

            return $this->redirect($this->generateUrl('changelog_show_all'));
        }

        return $this->render('changelog/changelog_create.html.twig', array(
            'form' => $form->createView(),
            'changelog' => $changeLogItem,
        ));
    }

    public function deleteChangeLogAction(ChangeLogItem $changeLogItem)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($changeLogItem);
        $em->flush();

        $this->addFlash("success", "\"".$changeLogItem->getTitle()."\" ble slettet");

        return $this->redirect($this->generateUrl('changelog_show_all'));
    }

    public function showAction()
    {
        $em = $this->getDoctrine()->getManager();
        $changeLogItems = $em->getRepository('AppBundle:ChangeLogItem')->findAllOrderedByDate();
        $changeLogItems = array_reverse($changeLogItems);

        return $this->render('changelog/changelog_show_all.html.twig', array('changeLogItems' => $changeLogItems));
    }
}
