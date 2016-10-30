<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AboutVektorController extends Controller
{
    public function showAction()
    {
        return $this->render('about/about_vektor.html.twig');
    }

    public function showFAQAction()
    {
        return $this->render('about/faq.html.twig');
    }
}
