<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TeamController extends Controller
{
    public function showAction($id)
    {
        $team = $this->getDoctrine()->getRepository('AppBundle:Team')->find($id);

        return $this->render('team/team_page.html.twig', array(
            'team'  => $team,
        ));
    }

    public function indexAction()
    {
        return $this->render('team/index.html.twig');
    }
}
