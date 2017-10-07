<?php

namespace AppBundle\Controller;

use AppBundle\Event\ReceiptEvent;
use AppBundle\Form\Type\ReceiptType;
use AppBundle\Role\Roles;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Receipt;
use AppBundle\Entity\User;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ReceiptController extends Controller
{
    public function showAction()
    {
        $usersWithReceipts = $this->getDoctrine()->getRepository('AppBundle:User')->findAllUsersWithReceipts();

        $sorter = $this->container->get('app.sorter');

        $sorter->sortUsersByReceiptSubmitTime($usersWithReceipts);
        $sorter->sortUsersByReceiptStatus($usersWithReceipts);

        return $this->render('receipt_admin/show_receipts.html.twig', array(
            'users_with_receipts' => $usersWithReceipts,
            'current_user' => $this->getUser(),
        ));
    }

    public function showIndividualAction(User $user)
    {
        $receipts = $this->getDoctrine()->getRepository('AppBundle:Receipt')->findByUser($user);

        $sorter = $this->container->get('app.sorter');
        $sorter->sortReceiptsBySubmitTime($receipts);
        $sorter->sortReceiptsByStatus($receipts);

        return $this->render('receipt_admin/show_individual_receipts.html.twig', array(
            'user' => $user,
            'receipts' => $receipts,
        ));
    }

    public function createAction(Request $request)
    {
        $receipt = new Receipt();
        $receipt->setUser($this->getUser());

        $receipts = $this->getDoctrine()->getRepository('AppBundle:Receipt')->findByUser($this->getUser());

        $sorter = $this->container->get('app.sorter');
        $sorter->sortReceiptsBySubmitTime($receipts);
        $sorter->sortReceiptsByStatus($receipts);

        $form = $this->createForm(ReceiptType::class, $receipt);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $isImageUpload = $request->files->get('receipt', ['picture_path']) !== null;
            if ($isImageUpload) {
                $path = $this->get('app.file_uploader')->uploadReceipt($request);
                $receipt->setPicturePath($path);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($receipt);
            $em->flush();

            $this->get('event_dispatcher')->dispatch(ReceiptEvent::CREATED, new ReceiptEvent($receipt));

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
                $this->get('app.file_uploader')->deleteReceipt($oldPicturePath);

                $path = $this->get('app.file_uploader')->uploadReceipt($request);
                $receipt->setPicturePath($path);
            } else {
                $receipt->setPicturePath($oldPicturePath);
            } // If a new image hasn't been uploaded

            $em = $this->getDoctrine()->getManager();
            $em->persist($receipt);
            $em->flush();

            $this->get('event_dispatcher')->dispatch(ReceiptEvent::EDITED, new ReceiptEvent($receipt));

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
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        if ($status === Receipt::STATUS_REFUNDED) {
            $this->get('event_dispatcher')->dispatch(ReceiptEvent::REFUNDED, new ReceiptEvent($receipt));
        } elseif ($status === Receipt::STATUS_REJECTED) {
            $this->get('event_dispatcher')->dispatch(ReceiptEvent::REJECTED, new ReceiptEvent($receipt));
        } elseif ($status === Receipt::STATUS_PENDING) {
            $this->get('event_dispatcher')->dispatch(ReceiptEvent::PENDING, new ReceiptEvent($receipt));
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
                $this->get('app.file_uploader')->deleteReceipt($oldPicturePath);

                $path = $this->get('app.file_uploader')->uploadReceipt($request);
                $receipt->setPicturePath($path);
            } else {
                $receipt->setPicturePath($oldPicturePath);
            } // If a new image hasn't been uploaded

            $em = $this->getDoctrine()->getManager();
            $em->persist($receipt);
            $em->flush();

            $this->get('event_dispatcher')->dispatch(ReceiptEvent::EDITED, new ReceiptEvent($receipt));

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
        $isTeamLeader = $this->get('app.roles')->userIsGranted($user, Roles::TEAM_LEADER);

        $userCanDeleteReceipt = $isTeamLeader || ($user == $receipt->getUser() && $receipt->getStatus() === Receipt::STATUS_PENDING);

        if (!$userCanDeleteReceipt) {
            throw new AccessDeniedException();
        }

        // Delete the image file
        $this->get('app.file_uploader')->deleteReceipt($receipt->getPicturePath());

        $em = $this->getDoctrine()->getManager();
        $em->remove($receipt);
        $em->flush();

        $this->get('event_dispatcher')->dispatch(ReceiptEvent::DELETED, new ReceiptEvent($receipt));

        return $this->redirect($request->headers->get('referer'));
    }
}
