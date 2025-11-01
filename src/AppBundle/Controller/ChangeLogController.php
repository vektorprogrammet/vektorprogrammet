<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ChangeLogItem;
use AppBundle\Form\Type\ChangeLogType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class ChangeLogController extends BaseController
{
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public function createChangeLogAction(Request $request)
    {
        $changeLogItem = new ChangeLogItem();
        $form = $this->createForm(ChangeLogType::class, $changeLogItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($changeLogItem);
            $this->entityManager->flush();

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
        $this->entityManager->remove($changeLogItem);
        $this->entityManager->flush();

        $this->addFlash("success", "\"".$changeLogItem->getTitle()."\" ble slettet");

        return $this->redirect($this->generateUrl('changelog_show_all'));
    }

    public function showAction()
    {
        $changeLogItems = $this->entityManager->getRepository(ChangeLogItem::class)->findAllOrderedByDate();
        $changeLogItems = array_reverse($changeLogItems);

        return $this->render('changelog/changelog_show_all.html.twig', array('changeLogItems' => $changeLogItems));
    }
}
