<?php

namespace App\Controller;

class TeacherController extends BaseController
{
    public function indexAction()
    {
        return $this->render('teacher/index.html.twig');
    }
}
