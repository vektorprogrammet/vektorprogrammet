<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    public function showAction()
    {
        return $this->render('home/index.html.twig');
    }
}
