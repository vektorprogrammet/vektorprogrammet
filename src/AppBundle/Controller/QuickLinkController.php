<?php

namespace AppBundle\Controller;


use AppBundle\Entity\QuickLink;
use AppBundle\Service\FileUploader;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\QuicklinkType;

class QuickLinkController extends BaseController
{
    public function showAction(Request $request)
    {
        $quickLinks = $this->getDoctrine()
            ->getRepository('AppBundle:quickLink')
            ->findAll();

        return $this->render('quick_link/quick_link_show_all.html.twig', array(
            'quickLinks' => $quickLinks,
        ));
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $em =$this->getDoctrine()->getManager();
        $quickLink=new QuickLink();

        $form=$this->createForm(QuicklinkType::class, $quickLink);

        $form->handleRequest($request);

        if ($form->isValid()){
            $em->persist($quickLink);
            $em->flush();
            return $this->redirectToRoute("quicklink_show");
        }


        return $this->render('quick_link/quick_link_edit.html.twig', array(
            'form' => $form->createView(),
        ));
     }


    public function editAction(Request $request)
    {



    }

    public function deleteAction(Request $request, QuickLink $quickLink)
    {
        if ($quickLink->getIconUrl()) {
            $this->get(FileUploader::class)->deleteFile($quickLink->getIconUrl());
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($quickLink);
        $em->flush();
        $this->addFlash("success", "Quicklink {$quickLink->getTitle()} ble slettet.");
        return $this->redirectToRoute("quicklink_show");
    }
}
