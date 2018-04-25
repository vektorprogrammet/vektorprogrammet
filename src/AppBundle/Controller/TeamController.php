<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TeamController extends Controller
{
    public function showAction($id)
    {
        $team = $this->getDoctrine()->getRepository('AppBundle:Team')->find($id);

        $sorter = $this->get('app.sorter');
        $filterService = $this->get('app.filter_service');

        $users = $team->getActiveUsers();
        $sorter->sortUsersByActiveTeamPosition($users);

        // Sort all the users' team histories by position
        // (So for example "Leder" comes before "Sekretær" if the user has multiple positions)
        foreach ($users as $user) {
            $activeTeamHistories = $filterService->filterWorkHistoriesByTeam($user->getActiveWorkHistories(), $team);
            $sorter->sortWorkHistoriesByPosition($activeTeamHistories);
            $user->setWorkHistories($activeTeamHistories);
        }

        return $this->render('team/team_page.html.twig', array(
            'team'  => $team,
            'users' => $users,
            'type' => 'team',
        ));
    }

    public function indexAction()
    {
        return $this->render('team/index.html.twig');
    }
}
