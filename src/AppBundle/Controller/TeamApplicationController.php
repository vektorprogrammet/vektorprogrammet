<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Team;
use AppBundle\Entity\TeamApplication;
use AppBundle\Entity\TeamMembership;
use AppBundle\Event\TeamApplicationCreatedEvent;
use AppBundle\Form\Type\TeamApplicationType;
use AppBundle\Role\Roles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TeamApplicationController extends BaseController
{
    private $entityManager;
    private $eventDispatcher;

    /**
     * @param EntityManagerInterface $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }
    public function showApplicationAction(TeamApplication $application)
    {
        $user = $this->getUser();
        $activeUserHistoriesInTeam = $this->entityManager->getRepository(TeamMembership::class)->findActiveTeamMembershipsByTeamAndUser($application->getTeam(), $user);
        if (empty($activeUserHistoriesInTeam) && !$this->isGranted(Roles::TEAM_LEADER)) {
            throw new AccessDeniedException();
        }

        return $this->render('team_admin/show_application.html.twig', array(
            'application' => $application,
        ));
    }

    public function showAllApplicationsAction(Team $team)
    {
        $applications = $this->entityManager->getRepository(TeamApplication::class)->findByTeam($team);
        $user = $this->getUser();
        $activeUserHistoriesInTeam = $this->entityManager->getRepository(TeamMembership::class)->findActiveTeamMembershipsByTeamAndUser($team, $user);
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
        $this->entityManager->remove($teamApplication);
        $this->entityManager->flush();

        return $this->redirectToRoute('team_application_show_all', array('id' => $teamApplication->getTeam()->getId()));
    }

    public function showAction(Team $team, Request $request)
    {
        if (!$team->getAcceptApplicationAndDeadline()) {
            throw new NotFoundHttpException();
        }
        $teamApplication = new TeamApplication();
        $form = $this->createForm(TeamApplicationType::class, $teamApplication);
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted() && $team->getAcceptApplicationAndDeadline()) {
            $teamApplication->setTeam($team);

            $this->entityManager->persist($teamApplication);
            $this->entityManager->flush();

            $this->eventDispatcher->dispatch(TeamApplicationCreatedEvent::NAME, new TeamApplicationCreatedEvent($teamApplication));

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
