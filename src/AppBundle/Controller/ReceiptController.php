<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\ReceiptType;
use AppBundle\FileSystem\FileUploader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Receipt;

class ReceiptController extends Controller
{
    public function showAction(Request $request)
    {
        $receipt = $this->getDoctrine()->getRepository('AppBundle:Receipt')->findByUser($this->getUser());

        return $this->render('receipt/show_receipts.html.twig', array(
            'receipt' => $receipt,
        ));
    }

    public function createAction(Request $request)
    {
        $receipt = new Receipt();
        $active_receipts = $this->getDoctrine()->getRepository('AppBundle:Receipt')->findActiveByUser($this->getUser());
        $inactive_receipts = $this->getDoctrine()->getRepository('AppBundle:Receipt')->findInactiveByUser($this->getUser());

        $form = $this->createForm(new ReceiptType(), $receipt);
        $form->handleRequest($request);


        return $this->render('receipt/create_receipt.twig', array(
            'form' => $form->createView(),
            'receipt' => $receipt,
            'active_receipts' => $active_receipts,
            'inactive_receipts' => $inactive_receipts,
        ));
    }
}
