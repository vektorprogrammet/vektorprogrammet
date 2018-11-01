<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ClientController extends BaseController
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
