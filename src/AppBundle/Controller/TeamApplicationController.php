<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Team;
use AppBundle\Entity\TeamApplication;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Request;

class TeamApplicationController extends Controller
{
    public function showAction(Team $team, Request $request)
    {
        $teamApplication = new TeamApplication();
        $form = $this->createForm(new TeamApplicationType(), $teamApplication);
        $form->handleRequest($request);
        return $this->render('team/team_application.html.twig',array(
            'team' => $team,
            'form' => $form->createView()
    ));

    }
}
