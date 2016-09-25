<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SchoolAllocationMainController extends Controller
{
    public function indexAction()
    {
        return $this->render('school_admin/school_allocate_main.html.twig');
    }
}