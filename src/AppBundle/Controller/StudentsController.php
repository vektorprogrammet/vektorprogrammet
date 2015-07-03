<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class StudentsController extends Controller
{
    public function showAction()
    {
        return $this->render('about/students.html.twig');
    }
}