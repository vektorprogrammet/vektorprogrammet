<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Receipt;
use AppBundle\Entity\User;
use AppBundle\Event\ReceiptEvent;
use AppBundle\Form\Type\ReceiptType;
use AppBundle\Repository\Contract\ReceiptRepositoryInterface;
use AppBundle\Repository\Contract\UserRepositoryInterface;
use AppBundle\Role\Roles;
use AppBundle\Service\Contract\FileUploaderInterface;
use AppBundle\Service\Contract\ReceiptStatisticsServiceInterface;
use AppBundle\Service\Contract\RoleManagerInterface;
use AppBundle\Service\Contract\SorterInterface;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ReceiptController extends BaseController
{
    private $userRepository;
    private $receiptRepository;
    private $sorter;
    private $fileUploader;
    private $roleManager;
    private $entityManager;
    private $eventDispatcher;
    private $receiptStatisticsService;

    /**
     * @param UserRepositoryInterface $userRepository
     * @param ReceiptRepositoryInterface $receiptRepository
     * @param SorterInterface $sorter
     * @param FileUploaderInterface $fileUploader
     * @param RoleManagerInterface $roleManager
     * @param EntityManagerInterface $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param ReceiptStatisticsServiceInterface $receiptStatisticsService
     */
    public function __construct(
        UserRepositoryInterface $userRepository,
        ReceiptRepositoryInterface $receiptRepository,
        SorterInterface $sorter,
        FileUploaderInterface $fileUploader,
        RoleManagerInterface $roleManager,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        ReceiptStatisticsServiceInterface $receiptStatisticsService
    ) {
        $this->userRepository = $userRepository;
        $this->receiptRepository = $receiptRepository;
        $this->sorter = $sorter;
        $this->fileUploader = $fileUploader;
        $this->roleManager = $roleManager;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->receiptStatisticsService = $receiptStatisticsService;
    }
    public function showAction()
    {
        $statistics = $this->receiptStatisticsService->calculateReceiptStatistics();

        return $this->render('receipt_admin/show_receipts.html.twig', array(
            'users_with_receipts' => $statistics['users_with_receipts'],
            'current_user' => $this->getUser(),
            'total_payout' => $statistics['total_payout'],
            'avg_refund_time_in_hours' => $statistics['avg_refund_time_in_hours'],
            'pending_statistics' => $statistics['pending_statistics'],
            'rejected_statistics' => $statistics['rejected_statistics'],
            'refunded_statistics' => $statistics['refunded_statistics']
        ));
    }

    public function showIndividualAction(User $user)
    {
        $receipts = $this->receiptStatisticsService->getSortedReceiptsForUser($user);

        return $this->render('receipt_admin/show_individual_receipts.html.twig', array(
            'user' => $user,
            'receipts' => $receipts,
        ));
    }

    public function createAction(Request $request)
    {
        $receipt = new Receipt();
        $receipt->setUser($this->getUser());

        $receipts = $this->receiptStatisticsService->getSortedReceiptsForUser($this->getUser());

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
        
        if ($status === $receipt->getStatus()) {
            return $this->redirectToRoute('receipts_show_individual', ['user' => $receipt->getUser()->getId()]);
        }

        $this->receiptStatisticsService->processStatusChange($receipt, $status);
        $this->entityManager->flush();

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
