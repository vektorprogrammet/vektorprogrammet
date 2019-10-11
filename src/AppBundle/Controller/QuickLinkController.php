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
        $quickLink=new QuickLink();

        $form=$this->createForm(QuicklinkType::class, $quickLink);

        $form->handleRequest($request);

        if ($form->isValid()){

            $imgPath= $this->get(FileUploader::class)->uploadQuicklink($request);
            $quickLink->setIconUrl($imgPath);
            //For testing purposes:
            $quickLink->setOrderNum(4);
            $quickLink->setVisible(true);

            //Test ended

            $em =$this->getDoctrine()->getManager();
            $em->persist($quickLink);
            $em->flush();

            $this->addFlash(
                "success",
                "Quicklink {$quickLink->getTitle()} ble opprettet"
            );


            return $this->redirectToRoute("quicklink_show");
        }


        return $this->render('quick_link/quick_link_edit.html.twig', array(
            'form' => $form->createView(),
            "create"=> true
        ));
     }




    public function editAction(QuickLink $quickLink, Request $request)
    {

        $oldImgPath = $quickLink->getIconUrl();


        $form=$this->createForm(QuicklinkType::class, $quickLink);

        $form->handleRequest($request);


        if ($form->isValid() && $form->isSubmitted()){
            if (!is_null($request->files->get('quicklink')['iconUrl'])){
                $imgPath= $this->get(FileUploader::class)->uploadQuicklink($request);
                $this->get(FileUploader::class)->deleteQuickLink($oldImgPath);

                $quickLink->setIconUrl($imgPath);

            }else{
                $quickLink->setIconUrl($oldImgPath);
            }
            $em =$this->getDoctrine()->getManager();
            $em->persist($quickLink);
            $em->flush();

            $this->addFlash(
                "success",
                "Quicklink {$quickLink->getTitle()} ble endret"
            );

            return $this->redirectToRoute("quicklink_show");
        }



        return $this->render('quick_link/quick_link_edit.html.twig', array(
            'form' => $form->createView(),
            "create"=> false
        ));

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
