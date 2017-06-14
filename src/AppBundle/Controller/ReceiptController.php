<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\ReceiptType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Receipt;
use AppBundle\Entity\User;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ReceiptController extends Controller
{
    public function showAction()
    {
        $department = $this->getUser()->getDepartment();
        //$active_receipts = $this->getDoctrine()->getRepository('AppBundle:Receipt')->findActiveByDepartment($department);
        //$inactive_receipts = $this->getDoctrine()->getRepository('AppBundle:Receipt')->findInactiveByDepartment($department);

        $usersWithActiveReceipts = $this->getDoctrine()->getRepository('AppBundle:User')->findAllUsersWithActiveReceipts();
        $usersWithInactiveReceipts = $this->getDoctrine()->getRepository('AppBundle:User')->findAllUsersWithInactiveReceipts();

        return $this->render('receipt_admin/show_receipts.html.twig', array(
            'users_with_active_receipts' => $usersWithActiveReceipts,
            'users_with_inactive_receipts' => $usersWithInactiveReceipts,
        ));
    }

    public function showIndividualAction(User $user)
    {
        $active_receipts = $this->getDoctrine()->getRepository('AppBundle:Receipt')->findActiveByUser($user);
        $inactive_receipts = $this->getDoctrine()->getRepository('AppBundle:Receipt')->findInactiveByUser($user);

        return $this->render('receipt_admin/show_individual_receipts.html.twig', array(
            'user' => $user,
            'active_receipts' => $active_receipts,
            'inactive_receipts' => $inactive_receipts,
        ));
    }

    public function createAction(Request $request)
    {
        $receipt = new Receipt();
        $receipt->setUser($this->getUser());

        $active_receipts = $this->getDoctrine()->getRepository('AppBundle:Receipt')->findActiveByUser($this->getUser());
        $inactive_receipts = $this->getDoctrine()->getRepository('AppBundle:Receipt')->findInactiveByUser($this->getUser());

        $form = $this->createForm(ReceiptType::class, $receipt, array(
            'required' => true,
        ));
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

            return $this->redirectToRoute('receipt_create');
        }

        return $this->render('receipt/create_receipt.twig', array(
            'form' => $form->createView(),
            'receipt' => $receipt,
            'active_receipts' => $active_receipts,
            'inactive_receipts' => $inactive_receipts,
        ));
    }

    public function deleteAdminAction(Receipt $receipt)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($receipt);
        $em->flush();

        return $this->redirectToRoute('receipts_show');
    }

    public function editAdminAction(Request $request, Receipt $receipt)
    {
        return $this->performEditAndRedirect($request, $receipt, 'receipts_show');
    }

    public function finishAdminAction(Receipt $receipt)
    {
        $receipt->setActive(false);

        $em = $this->getDoctrine()->getManager();
        $em->persist($receipt);
        $em->flush();

        // Send email
        $emailSender = $this->get('app.email_sender');
        $emailSender->sendPaidReceiptConfirmation($receipt);

        return $this->redirectToRoute('receipts_show_individual', array('user' => $receipt->getUser()->getId()));
    }

    public function deleteAction(Receipt $receipt)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($receipt);
        $em->flush();

        return $this->redirectToRoute('receipt_create');
    }

    public function editAction(Request $request, Receipt $receipt)
    {
        if ($this->getUser() != $receipt->getUser()) {
            throw new AccessDeniedHttpException();
        }

        return $this->performEditAndRedirect($request, $receipt, 'receipt_create');
    }

    public function performEditAndRedirect(Request $request, Receipt $receipt, string $redirectRoute)
    {
        $form = $this->createForm(ReceiptType::class, $receipt, array(
            'required' => false,
        ));
        $oldPicturePath = $receipt->getPicturePath();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $isImageUpload = array_values($request->files->get('receipt', ['picture_path']))[0] !== null;

            if ($isImageUpload) {
                $path = $this->get('app.file_uploader')->uploadReceipt($request);
                $receipt->setPicturePath($path);
            } else {
                $receipt->setPicturePath($oldPicturePath);
            } // If a new image hasn't been uploaded

            $em = $this->getDoctrine()->getManager();
            $em->persist($receipt);
            $em->flush();

            return $this->redirectToRoute('receipts_show_individual', array('user' => $receipt->getUser()->getId()));
        }

        return $this->render('receipt_admin/edit_receipt.twig', array(
            'form' => $form->createView(),
            'receipt' => $receipt,
        ));
    }
}
