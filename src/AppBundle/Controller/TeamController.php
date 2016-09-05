<?php

namespace AppBundle\Controller;

use AppBundle\Entity\WorkHistory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TeamController extends Controller
{
    public function showAction($id)
    {
        $team = $this->getDoctrine()->getRepository('AppBundle:Team')->find($id);
        $workHistories = $this->getDoctrine()->getRepository('AppBundle:WorkHistory')->findActiveWorkHistoriesByTeam($team);

        $leaders = array();
        $members = array();
        foreach ($workHistories as $workHistory) {
            $position = strtolower($workHistory->getPosition());
            if ($position === 'leder' || $position === 'nestleder') {
                $leaders[] = $workHistory;
            } else {
                $members[] = $workHistory;
            }
        }

        usort($members, function (WorkHistory $a, WorkHistory $b) {
            return strcmp(strtolower($a->getPosition()), strtolower($b->getPosition()));
        });

        usort($leaders, function (WorkHistory $a, WorkHistory $b) {
            return strcmp(strtolower($a->getPosition()), strtolower($b->getPosition()));
        });

        return $this->render('team/team_page.html.twig', array(
            'team' => $team,
            'workHistories' => array_merge($leaders, $members),
        ));
    }
}
