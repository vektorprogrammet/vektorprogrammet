<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SchoolsController extends Controller
{
    public function showAction()
    {
        return $this->render('about/schools.html.twig');
    }
}
