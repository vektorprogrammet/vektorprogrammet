<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class InfomeetingController extends Controller
{
    public function showAction(Department $department)
    {
        return $this->render('info_meeting/info_meeting_admin.html.twig',
            array('department' => $department));
    }
}
