<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ClientController extends Controller
{

    /**
     * @Route("/kontrollpanel/staging", name="staging_servers_show")
     * @Route("/assistent/min-side")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('client/index.html.twig');
    }
}
