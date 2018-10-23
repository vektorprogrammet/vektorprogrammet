<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Team;
use AppBundle\Entity\TeamApplication;
use AppBundle\Event\TeamApplicationCreatedEvent;
use AppBundle\Form\Type\TeamApplicationType;
use AppBundle\Role\Roles;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TeamApplicationController extends BaseController
{
    public function showApplicationAction(TeamApplication $application)
    {
        $user = $this->getUser();
        $activeUserHistoriesInTeam = $this->getDoctrine()->getRepository('AppBundle:TeamMembership')->findActiveTeamMembershipsByTeamAndUser($application->getTeam(), $user);
        if (empty($activeUserHistoriesInTeam) && !$this->isGranted(Roles::TEAM_LEADER)) {
            throw new AccessDeniedException();
        }

        return $this->render('team_admin/show_application.html.twig', array(
            'application' => $application,
        ));
    }

    public function showAllApplicationsAction(Team $team)
    {
        $applications = $this->getDoctrine()->getRepository('AppBundle:TeamApplication')->findByTeam($team);
        $user = $this->getUser();
        $activeUserHistoriesInTeam = $this->getDoctrine()->getRepository('AppBundle:TeamMembership')->findActiveTeamMembershipsByTeamAndUser($team, $user);
        if (empty($activeUserHistoriesInTeam) && !$this->isGranted(Roles::TEAM_LEADER)) {
            throw new AccessDeniedException();
        }

        return $this->render('team_admin/show_applications.html.twig', array(
            'applications' => $applications,
            'team' => $team,
        ));
    }

    public function deleteTeamApplicationByIdAction(TeamApplication $teamApplication)
    {
        $manager = $this->getDoctrine()->getManager();

        $manager->remove($teamApplication);
        $manager->flush();

        return $this->redirectToRoute('team_application_show_all', array('id' => $teamApplication->getTeam()->getId()));
    }

    public function showAction(Team $team, Request $request)
    {
        if (!$team->getAcceptApplication()) {
            throw new NotFoundHttpException();
        }
        $teamApplication = new TeamApplication();
        $form = $this->createForm(TeamApplicationType::class, $teamApplication);
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted() && $team->getAcceptApplication()) {
            $teamApplication->setTeam($team);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($teamApplication);
            $manager->flush();

            $this->get('event_dispatcher')->dispatch(TeamApplicationCreatedEvent::NAME, new TeamApplicationCreatedEvent($teamApplication));

            return $this->redirectToRoute('team_application_confirmation', array(
                'team_name' => $team->getName(),
            ));
        }

        return $this->render('team/team_application.html.twig', array(
            'team' => $team,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/team/application/bekreftelse/{team_name}", name="team_application_confirmation")
     * @return Response
     */
    public function confirmationAction($team_name)
    {
        return $this->render('team/confirmation.html.twig', array(
            'team_name' => $team_name,
        ));
    }
}
