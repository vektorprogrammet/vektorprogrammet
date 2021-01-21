<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends BaseController
{

    /**
     * @Route("/kontrollpanel/staging", name="staging_servers_show")
     * @Route("/assistent/min-side")
     * @Route("/party")
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('client/index.html.twig');
    }
}
