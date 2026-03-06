<?php

namespace App\Controller;

use App\Entity\Department;
use App\Entity\Repository\DepartmentRepository;
use App\Entity\Repository\PositionRepository;
use App\Entity\Repository\SemesterRepository;
use App\Entity\Repository\TeamMembershipRepository;
use App\Entity\Repository\TeamRepository;
use App\Entity\Team;
use App\Entity\TeamMembership;
use App\Event\TeamEvent;
use App\Event\TeamMembershipEvent;
use App\Form\Type\CreateTeamMembershipType;
use App\Form\Type\CreateTeamType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TeamAdminController extends BaseController
{
    public function __construct(
        private readonly TeamRepository $teamRepo,
        private readonly TeamMembershipRepository $teamMembershipRepo,
        private readonly PositionRepository $positionRepo,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly EntityManagerInterface $em,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    /**
     * @return Response
     */
    #[Route('/kontrollpanel/team/avdeling/{id}', name: 'teamadmin_show', defaults: ['id' => null], methods: ['GET'])]
    public function showAction(?Department $department = null)
    {
        if ($department === null) {
            $department = $this->getUser()->getDepartment();
        }

        // Find teams that are connected to the department of the user
        $activeTeams = $this->teamRepo->findActiveByDepartment($department);
        $inactiveTeams = $this->teamRepo->findInactiveByDepartment($department);

        // Return the view with suitable variables
        return $this->render('team_admin/index.html.twig', [
            'active_teams' => $activeTeams,
            'inactive_teams' => $inactiveTeams,
            'department' => $department,
        ]);
    }

    #[Route('/kontrollpanel/teamadmin/oppdater/teamhistorie/{id}', name: 'teamadmin_update_team_membership', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function updateTeamMembershipAction(Request $request, TeamMembership $teamMembership)
    {
        $department = $teamMembership->getTeam()->getDepartment();

        $form = $this->createForm(CreateTeamMembershipType::class, $teamMembership, [
            'department' => $department,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $teamMembership->setIsSuspended(false);
            $this->em->persist($teamMembership);
            $this->em->flush();

            $this->eventDispatcher->dispatch(new TeamMembershipEvent($teamMembership), TeamMembershipEvent::EDITED);

            return $this->redirect($this->generateUrl('teamadmin_show_specific_team', ['id' => $teamMembership->getTeam()->getId()]));
        }

        return $this->render('team_admin/create_team_membership.html.twig', [
            'form' => $form->createView(),
            'team' => $teamMembership->getTeam(),
            'teamMembership' => $teamMembership,
        ]);
    }

    #[Route('/kontrollpanel/teamadmin/team/nytt_medlem/{id}', name: 'teamadmin_add_user_to_team', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function addUserToTeamAction(Request $request, Team $team)
    {
        // Find the department of the team
        $department = $team->getDepartment();

        // Create a new TeamMembership entity
        $teamMembership = new TeamMembership();
        $teamMembership->setUser($this->getUser());
        $teamMembership->setPosition($this->positionRepo->findOneBy(['name' => 'Medlem']));

        // Create a new formType with the needed variables
        $form = $this->createForm(CreateTeamMembershipType::class, $teamMembership, [
            'department' => $department,
        ]);

        // Handle the form
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // set the team of the department
            $teamMembership->setTeam($team);

            // Persist the team to the database
            $this->em->persist($teamMembership);
            $this->em->flush();

            $this->eventDispatcher->dispatch(new TeamMembershipEvent($teamMembership), TeamMembershipEvent::CREATED);

            return $this->redirect($this->generateUrl('teamadmin_show_specific_team', ['id' => $team->getId()]));
        }

        return $this->render('team_admin/create_team_membership.html.twig', [
            'form' => $form->createView(),
            'team' => $team,
        ]);
    }

    #[Route('/kontrollpanel/teamadmin/team/{id}', name: 'teamadmin_show_specific_team', methods: ['GET'])]
    public function showSpecificTeamAction(Team $team)
    {
        // Find all TeamMembership entities based on team
        $activeTeamMemberships = $this->teamMembershipRepo->findActiveTeamMembershipsByTeam($team);
        $inActiveTeamMemberships = $this->teamMembershipRepo->findInactiveTeamMembershipsByTeam($team);
        usort($activeTeamMemberships, $this->sortTeamMembershipsByEndDate(...));
        usort($inActiveTeamMemberships, $this->sortTeamMembershipsByEndDate(...));

        $user = $this->getUser();
        $currentUserTeamMembership = $this->teamMembershipRepo->findActiveTeamMembershipsByUser($user);
        $isUserInTeam = false;
        foreach ($currentUserTeamMembership as $wh) {
            if (in_array($wh, $activeTeamMemberships)) {
                $isUserInTeam = true;
            }
        }

        // Return the view with suitable variables
        return $this->render('team_admin/specific_team.html.twig', [
            'team' => $team,
            'activeTeamMemberships' => $activeTeamMemberships,
            'inActiveTeamMemberships' => $inActiveTeamMemberships,
            'isUserInTeam' => $isUserInTeam,
        ]);
    }

    /**
     * @param TeamMembership $a
     * @param TeamMembership $b
     */
    private function sortTeamMembershipsByEndDate($a, $b): int
    {
        return $b->getStartSemester()->getStartDate() <=> $a->getStartSemester()->getStartDate();
    }

    #[Route('/kontrollpanel/teamadmin/update/{id}', name: 'teamadmin_update', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function updateTeamAction(Request $request, Team $team)
    {
        // Find the department of the team
        $department = $team->getDepartment();
        $oldTeamEmail = $team->getEmail();

        // Create the form
        $form = $this->createForm(CreateTeamType::class, $team);

        // Handle the form
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Don't persist if the preview button was clicked
            if (!$form->get('preview')->isClicked()) {
                // Persist the team to the database
                $this->em->persist($team);
                $this->em->flush();

                $this->eventDispatcher->dispatch(new TeamEvent($team, $oldTeamEmail), TeamEvent::EDITED);

                return $this->redirect($this->generateUrl('teamadmin_show'));
            }
            $teamMemberships = $this->teamMembershipRepo->findActiveTeamMembershipsByTeam($team);

            // Render the teampage as a preview
            return $this->render('team/team_page.html.twig', [
                'team' => $team,
                'teamMemberships' => $teamMemberships,
            ]);
        }

        return $this->render('team_admin/create_team.html.twig', [
            'team' => $team,
            'department' => $department,
            'form' => $form->createView(),
            'isUpdate' => true,
        ]);
    }

    #[Route('/kontrollpanel/teamadmin/avdeling/{id}', name: 'teamadmin_filter_teams_by_department', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function showTeamsByDepartmentAction(Department $department)
    {
        // Find teams that are connected to the department of the department ID sent in by the request
        $teams = $this->teamRepo->findByDepartment($department);

        // Return the view with suitable variables
        return $this->render('team_admin/index.html.twig', [
            'department' => $department,
            'teams' => $teams,
        ]);
    }

    #[Route('/kontrollpanel/teamadmin/avdeling/opprett/{id}', name: 'teamadmin_create_team_for_department', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function createTeamForDepartmentAction(Request $request, Department $department)
    {
        // Create a new Team entity
        $team = new Team();

        // Set the teams department to the department sent in by the request
        // Note: the team object is not valid without a department
        $team->setDepartment($department);

        // Create a new formType with the needed variables
        $form = $this->createForm(CreateTeamType::class, $team);

        // Handle the form
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Don't persist if the preview button was clicked
            if (!$form->get('preview')->isClicked()) {
                // Persist the team to the database
                $this->em->persist($team);
                $this->em->flush();

                $this->eventDispatcher->dispatch(new TeamEvent($team, $team->getEmail()), TeamEvent::CREATED);

                return $this->redirect($this->generateUrl('teamadmin_show'));
            }

            // Render the teampage as a preview
            return $this->render('team/team_page.html.twig', [
                'team' => $team,
                'teamMemberships' => [],
            ]);
        }

        return $this->render('team_admin/create_team.html.twig', [
            'form' => $form->createView(),
            'department' => $department,
            'team' => $team,
            'isUpdate' => false,
        ]);
    }

    #[Route('/kontrollpanel/teamadmin/team/slett/bruker/{id}', name: 'teamadmin_remove_user_from_team_by_id', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function removeUserFromTeamByIdAction(TeamMembership $teamMembership)
    {
        $this->em->remove($teamMembership);
        $this->em->flush();

        $this->eventDispatcher->dispatch(new TeamMembershipEvent($teamMembership), TeamMembershipEvent::DELETED);

        return $this->redirectToRoute('teamadmin_show_specific_team', ['id' => $teamMembership->getTeam()->getId()]);
    }

    #[Route('/kontrollpanel/teamadmin/slett/{id}', name: 'teamadmin_delete_team_by_id', methods: ['POST'])]
    public function deleteTeamByIdAction(Team $team)
    {
        foreach ($team->getTeamMemberships() as $teamMembership) {
            $teamMembership->setDeletedTeamName($team->getName());
            $this->em->persist($teamMembership);
        }

        $this->em->remove($team);
        $this->em->flush();

        return $this->redirectToRoute('teamadmin_show', ['id' => $team->getDepartment()->getId()]);
    }
}
