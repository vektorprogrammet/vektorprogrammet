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
        return $this->render('receipt/show_receipts.html.twig', array());
    }

    public function createAction(Request $request)
    {
        $receipt = new Receipt();

        $form = $this->createForm(new ReceiptType(), $receipt);
        $form->handleRequest($request);


        return $this->render('receipt/create_receipt.twig', array(
            'form' => $form->createView(),
        ));
    }
}
