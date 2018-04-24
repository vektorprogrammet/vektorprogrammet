<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TeamController extends Controller
{
    public function showAction($id)
    {
        $team = $this->getDoctrine()->getRepository('AppBundle:Team')->find($id);
        if (!$team->isActive() && !$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw new NotFoundHttpException('Team not found');
        }
        $workHistories = $this->getDoctrine()->getRepository('AppBundle:WorkHistory')->findActiveWorkHistoriesByTeam($team);

        return $this->render('team/team_page.html.twig', array(
            'team' => $team,
            'workHistories' => $workHistories,
        ));
    }

    public function indexAction()
    {
        return $this->render('team/index.html.twig');
    }
}
