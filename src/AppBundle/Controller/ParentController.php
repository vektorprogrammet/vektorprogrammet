<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ParentController extends Controller
{
    public function indexAction()
    {
        return $this->render('parent/index.html.twig');
    }
}
