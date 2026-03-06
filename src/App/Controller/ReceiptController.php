<?php

namespace App\Controller;

use App\Entity\Receipt;
use App\Entity\Repository\DepartmentRepository;
use App\Entity\Repository\ReceiptRepository;
use App\Entity\Repository\SemesterRepository;
use App\Entity\Repository\UserRepository;
use App\Entity\User;
use App\Event\ReceiptEvent;
use App\Form\Type\ReceiptType;
use App\Role\Roles;
use App\Service\FileUploader;
use App\Service\RoleManager;
use App\Service\Sorter;
use App\Utils\ReceiptStatistics;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ReceiptController extends BaseController
{
    public function __construct(
        private readonly UserRepository $userRepo,
        private readonly ReceiptRepository $receiptRepo,
        private readonly Sorter $sorter,
        private readonly FileUploader $fileUploader,
        private readonly RoleManager $roleManager,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly EntityManagerInterface $em,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    #[Route('/kontrollpanel/utlegg', name: 'receipts_show', methods: ['GET'])]
    public function showAction()
    {
        $usersWithReceipts = $this->userRepo->findAllUsersWithReceipts();
        $refundedReceipts = $this->receiptRepo->findByStatus(Receipt::STATUS_REFUNDED);
        $pendingReceipts = $this->receiptRepo->findByStatus(Receipt::STATUS_PENDING);
        $rejectedReceipts = $this->receiptRepo->findByStatus(Receipt::STATUS_REJECTED);

        $refundedReceiptStatistics = new ReceiptStatistics($refundedReceipts);
        $totalPayoutThisYear = $refundedReceiptStatistics->totalPayoutIn((new \DateTime())->format('Y'));
        $avgRefundTimeInHours = $refundedReceiptStatistics->averageRefundTimeInHours();

        $pendingReceiptStatistics = new ReceiptStatistics($pendingReceipts);
        $rejectedReceiptStatistics = new ReceiptStatistics($rejectedReceipts);

        $this->sorter->sortUsersByReceiptSubmitTime($usersWithReceipts);
        $this->sorter->sortUsersByReceiptStatus($usersWithReceipts);

        return $this->render('receipt_admin/show_receipts.html.twig', [
            'users_with_receipts' => $usersWithReceipts,
            'current_user' => $this->getUser(),
            'total_payout' => $totalPayoutThisYear,
            'avg_refund_time_in_hours' => $avgRefundTimeInHours,
            'pending_statistics' => $pendingReceiptStatistics,
            'rejected_statistics' => $rejectedReceiptStatistics,
            'refunded_statistics' => $refundedReceiptStatistics,
        ]);
    }

    #[Route('/kontrollpanel/utlegg/{user}', name: 'receipts_show_individual', methods: ['GET'])]
    public function showIndividualAction(User $user)
    {
        $receipts = $this->receiptRepo->findByUser($user);

        $this->sorter->sortReceiptsBySubmitTime($receipts);
        $this->sorter->sortReceiptsByStatus($receipts);

        return $this->render('receipt_admin/show_individual_receipts.html.twig', [
            'user' => $user,
            'receipts' => $receipts,
        ]);
    }

    #[Route('/utlegg', name: 'receipt_create', methods: ['GET', 'POST'])]
    public function createAction(Request $request)
    {
        $receipt = new Receipt();
        $receipt->setUser($this->getUser());

        $receipts = $this->receiptRepo->findByUser($this->getUser());

        $this->sorter->sortReceiptsBySubmitTime($receipts);
        $this->sorter->sortReceiptsByStatus($receipts);

        $form = $this->createForm(ReceiptType::class, $receipt);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $isImageUpload = $request->files->get('receipt', ['picture_path']) !== null;
            if ($isImageUpload) {
                $path = $this->fileUploader->uploadReceipt($request);
                $receipt->setPicturePath($path);
            }
            $this->em->persist($receipt);
            $this->em->flush();

            $this->eventDispatcher->dispatch(new ReceiptEvent($receipt), ReceiptEvent::CREATED);

            return $this->redirectToRoute('receipt_create');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $receipt->setPicturePath(null);
        }

        return $this->render('receipt/my_receipts.html.twig', [
            'form' => $form->createView(),
            'receipt' => $receipt,
            'receipts' => $receipts,
        ]);
    }

    #[Route('/utlegg/rediger/{receipt}', name: 'receipt_edit', requirements: ['receipt' => '\d+'], methods: ['GET', 'POST'])]
    public function editAction(Request $request, Receipt $receipt)
    {
        $user = $this->getUser();

        $userCanEditReceipt = $user === $receipt->getUser() && $receipt->getStatus() === Receipt::STATUS_PENDING;

        if (!$userCanEditReceipt) {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(ReceiptType::class, $receipt, [
            'picture_required' => false,
        ]);
        $oldPicturePath = $receipt->getPicturePath();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $isImageUpload = array_values($request->files->get('receipt', ['picture_path']))[0] !== null;

            if ($isImageUpload) {
                // Delete the old image file
                $this->fileUploader->deleteReceipt($oldPicturePath);

                $path = $this->fileUploader->uploadReceipt($request);
                $receipt->setPicturePath($path);
            } else {
                $receipt->setPicturePath($oldPicturePath);
            } // If a new image hasn't been uploaded

            $this->em->persist($receipt);
            $this->em->flush();

            $this->eventDispatcher->dispatch(new ReceiptEvent($receipt), ReceiptEvent::EDITED);

            return $this->redirectToRoute('receipt_create');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $receipt->setPicturePath($oldPicturePath);
        }

        return $this->render('receipt/edit_receipt.html.twig', [
            'form' => $form->createView(),
            'receipt' => $receipt,
            'parent_template' => 'base.html.twig',
        ]);
    }

    #[Route('/kontrollpanel/utlegg/status/{receipt}', name: 'receipt_edit_status', requirements: ['receipt' => '\d+'], methods: ['POST'])]
    public function editStatusAction(Request $request, Receipt $receipt)
    {
        $status = $request->get('status');
        if ($status !== Receipt::STATUS_PENDING
            && $status !== Receipt::STATUS_REFUNDED
            && $status !== Receipt::STATUS_REJECTED) {
            throw new BadRequestHttpException('Invalid status');
        }

        if ($status === $receipt->getStatus()) {
            return $this->redirectToRoute('receipts_show_individual', ['user' => $receipt->getUser()->getId()]);
        }

        $receipt->setStatus($status);
        if ($status === Receipt::STATUS_REFUNDED && !$receipt->getRefundDate()) {
            $receipt->setRefundDate(new \DateTime());
        }

        $this->em->flush();

        if ($status === Receipt::STATUS_REFUNDED) {
            $this->eventDispatcher->dispatch(new ReceiptEvent($receipt), ReceiptEvent::REFUNDED);
        } elseif ($status === Receipt::STATUS_REJECTED) {
            $this->eventDispatcher->dispatch(new ReceiptEvent($receipt), ReceiptEvent::REJECTED);
        } elseif ($status === Receipt::STATUS_PENDING) {
            $this->eventDispatcher->dispatch(new ReceiptEvent($receipt), ReceiptEvent::PENDING);
        }

        return $this->redirectToRoute('receipts_show_individual', ['user' => $receipt->getUser()->getId()]);
    }

    #[Route('/kontrollpanel/utlegg/rediger/{receipt}', name: 'receipt_admin_edit', requirements: ['receipt' => '\d+'], methods: ['GET', 'POST'])]
    public function adminEditAction(Request $request, Receipt $receipt)
    {
        $form = $this->createForm(ReceiptType::class, $receipt, [
            'picture_required' => false,
        ]);
        $oldPicturePath = $receipt->getPicturePath();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $isImageUpload = array_values($request->files->get('receipt', ['picture_path']))[0] !== null;

            if ($isImageUpload) {
                // Delete the old image file
                $this->fileUploader->deleteReceipt($oldPicturePath);

                $path = $this->fileUploader->uploadReceipt($request);
                $receipt->setPicturePath($path);
            } else {
                $receipt->setPicturePath($oldPicturePath);
            } // If a new image hasn't been uploaded

            $this->em->persist($receipt);
            $this->em->flush();

            $this->eventDispatcher->dispatch(new ReceiptEvent($receipt), ReceiptEvent::EDITED);

            return $this->redirectToRoute('receipts_show_individual', ['user' => $receipt->getUser()->getId()]);
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $receipt->setPicturePath($oldPicturePath);
        }

        return $this->render('receipt/edit_receipt.html.twig', [
            'form' => $form->createView(),
            'receipt' => $receipt,
            'parent_template' => 'adminBase.html.twig',
        ]);
    }

    #[Route('/utlegg/slett/{receipt}', name: 'receipt_delete', requirements: ['receipt' => '\d+'], methods: ['POST'])]
    public function deleteAction(Request $request, Receipt $receipt)
    {
        $user = $this->getUser();
        $isTeamLeader = $this->roleManager->userIsGranted($user, Roles::TEAM_LEADER);

        $userCanDeleteReceipt = $isTeamLeader || ($user === $receipt->getUser() && $receipt->getStatus() === Receipt::STATUS_PENDING);

        if (!$userCanDeleteReceipt) {
            throw new AccessDeniedException();
        }

        // Delete the image file
        $this->fileUploader->deleteReceipt($receipt->getPicturePath());

        $this->em->remove($receipt);
        $this->em->flush();

        $this->eventDispatcher->dispatch(new ReceiptEvent($receipt), ReceiptEvent::DELETED);

        $referer = $request->headers->get('referer');
        if ($referer !== null) {
            return $this->redirect($referer);
        }

        // Fallback: redirect to appropriate page based on user role
        return $isTeamLeader
            ? $this->redirectToRoute('receipts_show')
            : $this->redirectToRoute('receipt_create');
    }
}
