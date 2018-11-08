<?php

namespace AppBundle\Controller;

use AppBundle\Role\Roles;
use AppBundle\Entity\Team;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TeamController extends BaseController
{
    public function showAction(Team $team)
    {
        if (!$team->isActive() && !$this->isGranted(Roles::TEAM_MEMBER)) {
            throw new NotFoundHttpException('Team not found');
        }

        return $this->render('team/team_page.html.twig', array(
            'team'  => $team,
        ));
    }

    public function showByDepartmentAndTeamAction($departmentCity, $teamName)
    {
        $teams = $this->getDoctrine()->getRepository('AppBundle:Team')->findByCityAndName($departmentCity, $teamName);
        if (count($teams) !== 1) {
            throw new NotFoundHttpException('Team not found');
        }
        return $this->showAction($teams[0]);
    }

    public function indexAction()
    {
        return $this->render('team/index.html.twig');
    }
}
