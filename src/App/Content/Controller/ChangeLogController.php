<?php

namespace App\Content\Controller;

use App\Support\Controller\BaseController;
use App\Content\Infrastructure\Entity\ChangeLogItem;
use App\Content\Infrastructure\Repository\ChangeLogItemRepository;
use App\Entity\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Content\Form\ChangeLogType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ChangeLogController extends BaseController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ChangeLogItemRepository $changeLogItemRepo,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    #[Route('/kontrollpanel/changelog/create', name: 'changelog_create', methods: ['GET', 'POST'])]
    public function createChangeLogAction(Request $request)
    {
        $changeLogItem = new ChangeLogItem();
        $form = $this->createForm(ChangeLogType::class, $changeLogItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($changeLogItem);
            $this->em->flush();

            return $this->redirect($this->generateUrl('changelog_show_all'));
        }

        return $this->render('changelog/changelog_create.html.twig', [
            'form' => $form->createView(),
            'changelog' => $changeLogItem,
        ]);
    }

    #[Route('/kontrollpanel/changelog/edit/{id}', name: 'changelogitem_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function editChangeLogAction(Request $request, ChangeLogItem $changeLogItem)
    {
        $form = $this->createForm(ChangeLogType::class, $changeLogItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($changeLogItem);
            $this->em->flush();

            return $this->redirect($this->generateUrl('changelog_show_all'));
        }

        return $this->render('changelog/changelog_create.html.twig', [
            'form' => $form->createView(),
            'changelog' => $changeLogItem,
        ]);
    }

    #[Route('/kontrollpanel/changelog/delete/{id}', name: 'changelogitem_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteChangeLogAction(ChangeLogItem $changeLogItem)
    {
        $this->em->remove($changeLogItem);
        $this->em->flush();

        $this->addFlash('success', '"'.$changeLogItem->getTitle().'" ble slettet');

        return $this->redirect($this->generateUrl('changelog_show_all'));
    }

    #[Route('/kontrollpanel/changelog/show/all', name: 'changelog_show_all', methods: ['GET'])]
    public function showAction()
    {
        $changeLogItems = $this->changeLogItemRepo->findAllOrderedByDate();
        $changeLogItems = array_reverse($changeLogItems);

        return $this->render('changelog/changelog_show_all.html.twig', ['changeLogItems' => $changeLogItems]);
    }
}
