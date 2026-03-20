<?php

namespace App\Controller;

use App\Support\Controller\BaseController;
use App\Entity\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Entity\Repository\TeamApplicationRepository;
use App\Entity\Repository\TeamMembershipRepository;
use App\Entity\Team;
use App\Entity\TeamApplication;
use App\Event\TeamApplicationCreatedEvent;
use App\Form\Type\TeamApplicationType;
use App\Role\Roles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TeamApplicationController extends BaseController
{
    public function __construct(
        private readonly TeamMembershipRepository $teamMembershipRepo,
        private readonly TeamApplicationRepository $teamApplicationRepo,
        private readonly EntityManagerInterface $em,
        private readonly EventDispatcherInterface $eventDispatcher,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    #[Route('/kontrollpanel/team/application/{id}', name: 'team_application_show', methods: ['GET'])]
    public function showApplicationAction(TeamApplication $application)
    {
        $user = $this->getUser();
        $activeUserHistoriesInTeam = $this->teamMembershipRepo->findActiveTeamMembershipsByTeamAndUser($application->getTeam(), $user);
        if (empty($activeUserHistoriesInTeam) && !$this->isGranted(Roles::TEAM_LEADER)) {
            throw new AccessDeniedException();
        }

        return $this->render('team_admin/show_application.html.twig', [
            'application' => $application,
        ]);
    }

    #[Route('/kontrollpanel/team/applications/{id}', name: 'team_application_show_all', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function showAllApplicationsAction(Team $team)
    {
        $applications = $this->teamApplicationRepo->findByTeam($team);
        $user = $this->getUser();
        $activeUserHistoriesInTeam = $this->teamMembershipRepo->findActiveTeamMembershipsByTeamAndUser($team, $user);
        if (empty($activeUserHistoriesInTeam) && !$this->isGranted(Roles::TEAM_LEADER)) {
            throw new AccessDeniedException();
        }

        return $this->render('team_admin/show_applications.html.twig', [
            'applications' => $applications,
            'team' => $team,
        ]);
    }

    #[Route('/kontrollpanel/team/applications/slett/{id}', name: 'team_application_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteTeamApplicationByIdAction(TeamApplication $teamApplication)
    {
        $this->em->remove($teamApplication);
        $this->em->flush();

        return $this->redirectToRoute('team_application_show_all', ['id' => $teamApplication->getTeam()->getId()]);
    }

    #[Route('/team/application/{id}', name: 'team_application', methods: ['GET', 'POST'])]
    public function showAction(Team $team, Request $request)
    {
        if (!$team->getAcceptApplicationAndDeadline()) {
            throw new NotFoundHttpException();
        }
        $teamApplication = new TeamApplication();
        $form = $this->createForm(TeamApplicationType::class, $teamApplication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $team->getAcceptApplicationAndDeadline()) {
            $teamApplication->setTeam($team);

            $this->em->persist($teamApplication);
            $this->em->flush();

            $this->eventDispatcher->dispatch(new TeamApplicationCreatedEvent($teamApplication), TeamApplicationCreatedEvent::NAME);

            return $this->redirectToRoute('team_application_confirmation', [
                'team_name' => $team->getName(),
            ]);
        }

        return $this->render('team/team_application.html.twig', [
            'team' => $team,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return Response
     */
    #[Route('/team/application/bekreftelse/{team_name}', name: 'team_application_confirmation')]
    public function confirmationAction($team_name)
    {
        return $this->render('team/confirmation.html.twig', [
            'team_name' => $team_name,
        ]);
    }
}
