<?php

namespace AppBundle\Controller;

use AppBundle\Event\ReceiptCreatedEvent;
use AppBundle\Form\Type\ReceiptType;
use AppBundle\Role\Roles;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Receipt;
use AppBundle\Entity\User;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ReceiptController extends Controller
{
    public function showAction()
    {
        $usersWithActiveReceipts = $this->getDoctrine()->getRepository('AppBundle:User')->findAllUsersWithActiveReceipts();
        $usersWithInactiveReceipts = $this->getDoctrine()->getRepository('AppBundle:User')->findAllUsersWithInactiveReceipts();

        return $this->render('receipt_admin/show_receipts.html.twig', array(
            'users_with_active_receipts' => $usersWithActiveReceipts,
            'users_with_inactive_receipts' => $usersWithInactiveReceipts,
            'current_user' => $this->getUser(),
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

            $this->get('event_dispatcher')->dispatch(ReceiptCreatedEvent::NAME, new ReceiptCreatedEvent($receipt));

            return $this->redirectToRoute('receipt_create');
        }

        return $this->render('receipt/my_receipts.html.twig', array(
            'form' => $form->createView(),
            'receipt' => $receipt,
            'active_receipts' => $active_receipts,
            'inactive_receipts' => $inactive_receipts,
        ));
    }

    public function editAction(Request $request, Receipt $receipt)
    {
        $user = $this->getUser();
        $isTeamLeader = $this->get('app.roles')->userIsGranted($user, Roles::TEAM_LEADER);

        if (!$isTeamLeader && ($user !== $receipt->getUser() || !$receipt->isActive())) {
            throw new AccessDeniedHttpException();
        }

        $form = $this->createForm(ReceiptType::class, $receipt, array(
            'picture_required' => false,
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

            if ($user === $receipt->getUser()) {
                return $this->redirectToRoute('receipt_create');
            } else {
                return $this->redirectToRoute('receipts_show_individual', array('user' => $receipt->getUser()->getId()));
            }
        }

        $parentTemplate = ($user === $receipt->getUser()) ? 'base.html.twig' : 'adminBase.html.twig';

        return $this->render('receipt/edit_receipt.html.twig', array(
            'form' => $form->createView(),
            'receipt' => $receipt,
            'parent_template' => $parentTemplate,
        ));
    }

    public function finishAdminAction(Receipt $receipt)
    {
        $user = $this->getUser();
        $isTeamLeader = $this->get('app.roles')->userIsGranted($user, Roles::TEAM_LEADER);
        if (!$isTeamLeader) {
            throw new AccessDeniedHttpException();
        }

        $alreadyFinished = !$receipt->isActive();
        if ($alreadyFinished) {
            throw new BadRequestHttpException();
        }

        $receipt->setActive(false);

        $em = $this->getDoctrine()->getManager();
        $em->persist($receipt);
        $em->flush();

        // Send email
        $emailSender = $this->get('app.email_sender');
        $emailSender->sendPaidReceiptConfirmation($receipt);

        if ($user === $receipt->getUser()) {
            return $this->redirectToRoute('receipt_create');
        } else {
            return $this->redirectToRoute('receipts_show_individual', array('user' => $receipt->getUser()->getId()));
        }
    }

    public function deleteAction(Request $request, Receipt $receipt)
    {
        $user = $this->getUser();
        $isTeamLeader = $this->get('app.roles')->userIsGranted($user, Roles::TEAM_LEADER);

        if (!$isTeamLeader && ($user !== $receipt->getUser() || !$receipt->isActive())) {
            throw new AccessDeniedHttpException();
        }


        $em = $this->getDoctrine()->getManager();
        $em->remove($receipt);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }
}
