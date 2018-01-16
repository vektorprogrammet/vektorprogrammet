<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class InfomeetingController extends Controller
{
    public function showAction()
    {
        return $this->render('info_meeting/info_meeting_admin.html.twig');
    }
}
