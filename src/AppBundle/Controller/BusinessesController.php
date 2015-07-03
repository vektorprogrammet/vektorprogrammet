<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BusinessesController extends Controller
{
    public function showAction()
    {
        return $this->render('about/businesses.html.twig');
    }
}