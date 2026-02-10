<?php


namespace App\Controller;

use App\Entity\ChangeLogItem;
use App\Entity\Repository\ChangeLogItemRepository;
use App\Entity\Repository\DepartmentRepository;
use App\Entity\Repository\SemesterRepository;
use App\Form\Type\ChangeLogType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class ChangeLogController extends BaseController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ChangeLogItemRepository $changeLogItemRepo,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

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

        return $this->render('changelog/changelog_create.html.twig', array(
            'form' => $form->createView(),
            'changelog' => $changeLogItem,
        ));
    }

    public function editChangeLogAction(Request $request, ChangeLogItem $changeLogItem)
    {
        $form = $this->createForm(ChangeLogType::class, $changeLogItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($changeLogItem);
            $this->em->flush();

            return $this->redirect($this->generateUrl('changelog_show_all'));
        }

        return $this->render('changelog/changelog_create.html.twig', array(
            'form' => $form->createView(),
            'changelog' => $changeLogItem,
        ));
    }

    public function deleteChangeLogAction(ChangeLogItem $changeLogItem)
    {
        $this->em->remove($changeLogItem);
        $this->em->flush();

        $this->addFlash("success", "\"".$changeLogItem->getTitle()."\" ble slettet");

        return $this->redirect($this->generateUrl('changelog_show_all'));
    }

    public function showAction()
    {
        $changeLogItems = $this->changeLogItemRepo->findAllOrderedByDate();
        $changeLogItems = array_reverse($changeLogItems);

        return $this->render('changelog/changelog_show_all.html.twig', array('changeLogItems' => $changeLogItems));
    }
}
