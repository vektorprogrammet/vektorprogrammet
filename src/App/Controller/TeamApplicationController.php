<?php

namespace App\Controller;

use App\Entity\Repository\DepartmentRepository;
use App\Entity\Repository\SemesterRepository;
use App\Entity\Repository\TeamApplicationRepository;
use App\Entity\Repository\TeamMembershipRepository;
use App\Entity\Team;
use App\Entity\TeamApplication;
use App\Entity\TeamMembership;
use App\Event\TeamApplicationCreatedEvent;
use App\Form\Type\TeamApplicationType;
use App\Role\Roles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TeamApplicationController extends BaseController
{
    public function __construct(
        private TeamMembershipRepository $teamMembershipRepo,
        private TeamApplicationRepository $teamApplicationRepo,
        private EntityManagerInterface $em,
        private EventDispatcherInterface $eventDispatcher,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    public function showApplicationAction(TeamApplication $application)
    {
        $user = $this->getUser();
        $activeUserHistoriesInTeam = $this->teamMembershipRepo->findActiveTeamMembershipsByTeamAndUser($application->getTeam(), $user);
        if (empty($activeUserHistoriesInTeam) && !$this->isGranted(Roles::TEAM_LEADER)) {
            throw new AccessDeniedException();
        }

        return $this->render('team_admin/show_application.html.twig', array(
            'application' => $application,
        ));
    }

    public function showAllApplicationsAction(Team $team)
    {
        $applications = $this->teamApplicationRepo->findByTeam($team);
        $user = $this->getUser();
        $activeUserHistoriesInTeam = $this->teamMembershipRepo->findActiveTeamMembershipsByTeamAndUser($team, $user);
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
        $this->em->remove($teamApplication);
        $this->em->flush();

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

        if ($form->isSubmitted() && $form->isValid() && $team->getAcceptApplicationAndDeadline()) {
            $teamApplication->setTeam($team);

            $this->em->persist($teamApplication);
            $this->em->flush();

            $this->eventDispatcher->dispatch(new TeamApplicationCreatedEvent($teamApplication), TeamApplicationCreatedEvent::NAME);

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
     * @return Response
     */
    #[Route("/team/application/bekreftelse/{team_name}", name: "team_application_confirmation")]
    public function confirmationAction($team_name)
    {
        return $this->render('team/confirmation.html.twig', array(
            'team_name' => $team_name,
        ));
    }
}
