<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TeacherController extends Controller
{
    public function indexAction()
    {
        return $this->render('teacher/index.html.twig');
    }
}
