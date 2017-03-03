<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\ReceiptType;
use AppBundle\FileSystem\FileUploader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Receipt;

class ReceiptController extends Controller
{
    public function showAction()
    {
        $department = $this->getUser()->getDepartment();
        $receipt = $this->getDoctrine()->getRepository('AppBundle:Receipt')->findByDepartment($department);
        $active_receipts = $this->getDoctrine()->getRepository('AppBundle:Receipt')->findActiveByDepartment($department);
        $inactive_receipts = $this->getDoctrine()->getRepository('AppBundle:Receipt')->findInactiveByDepartment($department);

        return $this->render('receipt_admin/show_receipts.html.twig', array(
            'receipt' => $receipt,
            'active_receipts' => $active_receipts,
            'inactive_receipts' => $inactive_receipts,
        ));
    }

    // TODO: Write this one
    public function showIndividualAction(Receipt $receipt)
    {
        $active_receipts = $this->getDoctrine()->getRepository('AppBundle:Receipt')->findActiveByUser($receipt->getUser());
        $inactive_receipts = $this->getDoctrine()->getRepository('AppBundle:Receipt')->findInactiveByUser($receipt->getUser());

        return $this->render('receipt_admin/show_individual_receipts.html.twig', array(
            'receipt' => $receipt,
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

        $form = $this->createForm(new ReceiptType(), $receipt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
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

    public function deleteAction(Request $request){

    }

    public function editAction(Request $request){

    }
}
