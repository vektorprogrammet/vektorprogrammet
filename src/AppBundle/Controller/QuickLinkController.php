<?php

namespace AppBundle\Controller;


class QuickLinkController extends BaseController
{
    public function showAction()
    {
        $quickLinks = $this->getDoctrine()
            ->getRepository('AppBundle:quickLink')
            ->findAll();

        return $this->render('quick_link/quick_link_show_all.html.twig', array(
            'quickLinks' => $quickLinks,
        ));
    }
}
