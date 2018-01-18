<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ContactController extends Controller
{
    /**
     * @Route("/kontakt", name="contact")
     */
    public function indexAction()
    {
        $_SERVER['http_client_ip'] = '82.102.27.50';
        return $this->render('contact/index.html.twig');
    }
}
