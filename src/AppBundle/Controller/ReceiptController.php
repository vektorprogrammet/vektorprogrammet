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

    public function showIndividualAction(Request $request)
    {
        $receipt = $this->getDoctrine()->getRepository('AppBundle:Receipt')->findByUser($this->getUser());
        $active_receipts = $this->getDoctrine()->getRepository('AppBundle:Receipt')->findActiveByUser($this->getUser());
        $inactive_receipts = $this->getDoctrine()->getRepository('AppBundle:Receipt')->findInactiveByUser($this->getUser());

        return $this->render('receipt/show_receipts.html.twig', array(
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
                //First move the signature image file to its folder
                $targetFolder = $this->container->getParameter('receipt_images') . '/';
                //Create a FileUploader with target folder and allowed file types as parameters
                $uploader = new FileUploader($targetFolder);
                //Move the file to target folder

                $result = $uploader->upload($request);
                $path = $result[array_keys($result)[0]];
                $fileName = substr($path, strrpos($path, '/') + 1);
                $receipt->setPicturePath('/images/receipts/' . $fileName);
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
