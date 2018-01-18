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
        return $this->render('contact/index.html.twig');
    }
}
