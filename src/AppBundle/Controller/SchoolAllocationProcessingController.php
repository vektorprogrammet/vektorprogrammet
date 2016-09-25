<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SchoolAllocationProcessingController extends Controller
{
    public function indexAction()
    {
        return $this->render('school_admin/school_allocate_processing.html.twig');
    }
}