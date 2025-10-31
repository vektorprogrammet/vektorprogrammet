<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Receipt;
use AppBundle\Entity\User;
use AppBundle\Event\ReceiptEvent;
use AppBundle\Form\Type\ReceiptType;
use AppBundle\Role\Roles;
use AppBundle\Repository\Contract\ReceiptRepositoryInterface;
use AppBundle\Repository\Contract\UserRepositoryInterface;
use AppBundle\Service\Contract\FileUploaderInterface;
use AppBundle\Service\Contract\RoleManagerInterface;
use AppBundle\Service\Contract\SorterInterface;
use AppBundle\Utils\ReceiptStatistics;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ReceiptController extends BaseController
{
    private $receiptRepository;
    private $userRepository;
    private $sorter;
    private $fileUploader;
    private $roleManager;
    private $entityManager;
    private $eventDispatcher;

    /**
     * @param ReceiptRepositoryInterface $receiptRepository
     * @param UserRepositoryInterface $userRepository
     * @param SorterInterface $sorter
     * @param FileUploaderInterface $fileUploader
     * @param RoleManagerInterface $roleManager
     * @param EntityManagerInterface $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        ReceiptRepositoryInterface $receiptRepository,
        UserRepositoryInterface $userRepository,
        SorterInterface $sorter,
        FileUploaderInterface $fileUploader,
        RoleManagerInterface $roleManager,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->receiptRepository = $receiptRepository;
        $this->userRepository = $userRepository;
        $this->sorter = $sorter;
        $this->fileUploader = $fileUploader;
        $this->roleManager = $roleManager;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function showAction()
    {
        $usersWithReceipts = $this->userRepository->findAllUsersWithReceipts();
        $refundedReceipts = $this->receiptRepository->findByStatus(Receipt::STATUS_REFUNDED);
        $pendingReceipts = $this->receiptRepository->findByStatus(Receipt::STATUS_PENDING);
        $rejectedReceipts = $this->receiptRepository->findByStatus(Receipt::STATUS_REJECTED);

        $refundedReceiptStatistics = new ReceiptStatistics($refundedReceipts);
        $totalPayoutThisYear = $refundedReceiptStatistics->totalPayoutIn((new DateTime())->format('Y'));
        $avgRefundTimeInHours = $refundedReceiptStatistics->averageRefundTimeInHours();

        $pendingReceiptStatistics = new ReceiptStatistics($pendingReceipts);
        $rejectedReceiptStatistics = new ReceiptStatistics($rejectedReceipts);

        $this->sorter->sortUsersByReceiptSubmitTime($usersWithReceipts);
        $this->sorter->sortUsersByReceiptStatus($usersWithReceipts);

        return $this->render('receipt_admin/show_receipts.html.twig', array(
            'users_with_receipts' => $usersWithReceipts,
            'current_user' => $this->getUser(),
            'total_payout' => $totalPayoutThisYear,
            'avg_refund_time_in_hours' => $avgRefundTimeInHours,
            'pending_statistics' => $pendingReceiptStatistics,
            'rejected_statistics' => $rejectedReceiptStatistics,
            'refunded_statistics' => $refundedReceiptStatistics
        ));
    }

    public function showIndividualAction(User $user)
    {
        $receipts = $this->receiptRepository->findByUser($user);

        $this->sorter->sortReceiptsBySubmitTime($receipts);
        $this->sorter->sortReceiptsByStatus($receipts);

        return $this->render('receipt_admin/show_individual_receipts.html.twig', array(
            'user' => $user,
            'receipts' => $receipts,
        ));
    }

    public function createAction(Request $request)
    {
        $receipt = new Receipt();
        $receipt->setUser($this->getUser());

        $receipts = $this->receiptRepository->findByUser($this->getUser());

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
            $this->entityManager->persist($receipt);
            $this->entityManager->flush();

            $this->eventDispatcher->dispatch(ReceiptEvent::CREATED, new ReceiptEvent($receipt));

            return $this->redirectToRoute('receipt_create');
        }

        if (!$form->isValid()) {
            $receipt->setPicturePath(null);
        }

        return $this->render('receipt/my_receipts.html.twig', array(
            'form' => $form->createView(),
            'receipt' => $receipt,
            'receipts' => $receipts,
        ));
    }

    public function editAction(Request $request, Receipt $receipt)
    {
        $user = $this->getUser();

        $userCanEditReceipt = $user === $receipt->getUser() && $receipt->getStatus() === Receipt::STATUS_PENDING;

        if (!$userCanEditReceipt) {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(ReceiptType::class, $receipt, array(
            'picture_required' => false,
        ));
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

            $this->entityManager->persist($receipt);
            $this->entityManager->flush();

            $this->eventDispatcher->dispatch(ReceiptEvent::EDITED, new ReceiptEvent($receipt));

            return $this->redirectToRoute('receipt_create');
        }

        if (!$form->isValid()) {
            $receipt->setPicturePath($oldPicturePath);
        }

        return $this->render('receipt/edit_receipt.html.twig', array(
            'form' => $form->createView(),
            'receipt' => $receipt,
            'parent_template' => 'base.html.twig',
        ));
    }

    public function editStatusAction(Request $request, Receipt $receipt)
    {
        $status = $request->get('status');
        if ($status !== Receipt::STATUS_PENDING &&
            $status !== Receipt::STATUS_REFUNDED &&
            $status !== Receipt::STATUS_REJECTED) {
            throw new BadRequestHttpException('Invalid status');
        }

        if ($status === $receipt->getStatus()) {
            return $this->redirectToRoute('receipts_show_individual', ['user' => $receipt->getUser()->getId()]);
        }

        $receipt->setStatus($status);
        if ($status === Receipt::STATUS_REFUNDED && !$receipt->getRefundDate()) {
            $receipt->setRefundDate(new DateTime());
        }

        $this->entityManager->flush();

        if ($status === Receipt::STATUS_REFUNDED) {
            $this->eventDispatcher->dispatch(ReceiptEvent::REFUNDED, new ReceiptEvent($receipt));
        } elseif ($status === Receipt::STATUS_REJECTED) {
            $this->eventDispatcher->dispatch(ReceiptEvent::REJECTED, new ReceiptEvent($receipt));
        } elseif ($status === Receipt::STATUS_PENDING) {
            $this->eventDispatcher->dispatch(ReceiptEvent::PENDING, new ReceiptEvent($receipt));
        }

        return $this->redirectToRoute('receipts_show_individual', ['user' => $receipt->getUser()->getId()]);
    }

    public function adminEditAction(Request $request, Receipt $receipt)
    {
        $form = $this->createForm(ReceiptType::class, $receipt, array(
            'picture_required' => false,
        ));
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

            $this->entityManager->persist($receipt);
            $this->entityManager->flush();

            $this->eventDispatcher->dispatch(ReceiptEvent::EDITED, new ReceiptEvent($receipt));

            return $this->redirectToRoute('receipts_show_individual', array('user' => $receipt->getUser()->getId()));
        }

        if (!$form->isValid()) {
            $receipt->setPicturePath($oldPicturePath);
        }

        return $this->render('receipt/edit_receipt.html.twig', array(
            'form' => $form->createView(),
            'receipt' => $receipt,
            'parent_template' => 'adminBase.html.twig',
        ));
    }

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

        $this->entityManager->remove($receipt);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(ReceiptEvent::DELETED, new ReceiptEvent($receipt));

        return $this->redirect($request->headers->get('referer'));
    }
}
