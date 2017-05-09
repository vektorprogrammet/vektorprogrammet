<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TeamController extends Controller
{
    public function showAction($id)
    {
        $team = $this->getDoctrine()->getRepository('AppBundle:Team')->find($id);
        $workHistories = $this->getDoctrine()->getRepository('AppBundle:WorkHistory')->findActiveWorkHistoriesByTeam($team);
        $sortedTeamMembers = $this->container->get('group_sorter')->getSortedTeamMembers($workHistories);

        return $this->render('team/team_page.html.twig', array(
            'team' => $team,
            'workHistories' => $sortedTeamMembers,
        ));
    }
}
