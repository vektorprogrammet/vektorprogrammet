<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use AppBundle\Event\TeamEvent;
use AppBundle\Event\TeamMembershipEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Team;
use AppBundle\Form\Type\CreateTeamType;
use AppBundle\Entity\TeamMembership;
use AppBundle\Form\Type\CreateTeamMembershipType;

class TeamAdminController extends BaseController
{
    /**
     * @Route("/kontrollpanel/team/avdeling/{id}", name="teamadmin_show", defaults={"id":null}, methods={"GET"})
     * @param Department|null $department
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Department $department = null)
    {
        if ($department === null) {
            $department = $this->getUser()->getDepartment();
        }

        // Find teams that are connected to the department of the user
        $activeTeams   = $this->getDoctrine()->getRepository('AppBundle:Team')->findActiveByDepartment($department);
        $inactiveTeams = $this->getDoctrine()->getRepository('AppBundle:Team')->findInactiveByDepartment($department);

        // Return the view with suitable variables
        return $this->render('team_admin/index.html.twig', array(
            'active_teams'   => $activeTeams,
            'inactive_teams' => $inactiveTeams,
            'department'     => $department,
        ));
    }

    public function updateTeamMembershipAction(Request $request, TeamMembership $teamMembership)
    {
        $department = $teamMembership->getTeam()->getDepartment();

        $form = $this->createForm(CreateTeamMembershipType::class, $teamMembership, [
            'department' => $department
        ]);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $teamMembership->setIsSuspended(false);
            $em = $this->getDoctrine()->getManager();
            $em->persist($teamMembership);
            $em->flush();

            $this->get('event_dispatcher')->dispatch(TeamMembershipEvent::EDITED, new TeamMembershipEvent($teamMembership));

            return $this->redirect($this->generateUrl('teamadmin_show_specific_team', array( 'id' => $teamMembership->getTeam()->getId() )));
        }

        return $this->render('team_admin/create_team_membership.html.twig', array(
            'form' => $form->createView(),
            'team' => $teamMembership->getTeam(),
            'teamMembership' => $teamMembership
        ));
    }

    public function addUserToTeamAction(Request $request, Team $team)
    {
        // Find the department of the team
        $department = $team->getDepartment();

        // Create a new TeamMembership entity
        $teamMembership = new TeamMembership();
        $teamMembership->setUser($this->getUser());
        $teamMembership->setPosition($this->getDoctrine()->getRepository('AppBundle:Position')->findOneBy(array( 'name' => 'Medlem' )));

        // Create a new formType with the needed variables
        $form = $this->createForm(CreateTeamMembershipType::class, $teamMembership, [
            'department' => $department
        ]);

        // Handle the form
        $form->handleRequest($request);

        if ($form->isValid()) {

            //set the team of the department
            $teamMembership->setTeam($team);

            // Persist the team to the database
            $em = $this->getDoctrine()->getManager();
            $em->persist($teamMembership);
            $em->flush();

            $this->get('event_dispatcher')->dispatch(TeamMembershipEvent::CREATED, new TeamMembershipEvent($teamMembership));

            return $this->redirect($this->generateUrl('teamadmin_show_specific_team', array( 'id' => $team->getId() )));
        }

        return $this->render('team_admin/create_team_membership.html.twig', array(
            'form' => $form->createView(),
            'team' => $team
        ));
    }

    public function showSpecificTeamAction(Team $team)
    {
        // Find all TeamMembership entities based on team
        $activeTeamMemberships   = $this->getDoctrine()->getRepository('AppBundle:TeamMembership')->findActiveTeamMembershipsByTeam($team);
        $inActiveTeamMemberships = $this->getDoctrine()->getRepository('AppBundle:TeamMembership')->findInactiveTeamMembershipsByTeam($team);
        usort($activeTeamMemberships, array( $this, 'sortTeamMembershipsByEndDate' ));
        usort($inActiveTeamMemberships, array( $this, 'sortTeamMembershipsByEndDate' ));

        $user                      = $this->getUser();
        $currentUserTeamMembership = $this->getDoctrine()->getRepository('AppBundle:TeamMembership')->findActiveTeamMembershipsByUser($user);
        $isUserInTeam              = false;
        foreach ($currentUserTeamMembership as $wh) {
            if (in_array($wh, $activeTeamMemberships)) {
                $isUserInTeam = true;
            }
        }

        // Return the view with suitable variables
        return $this->render('team_admin/specific_team.html.twig', array(
            'team'                    => $team,
            'activeTeamMemberships'   => $activeTeamMemberships,
            'inActiveTeamMemberships' => $inActiveTeamMemberships,
            'isUserInTeam'            => $isUserInTeam,
        ));
    }

    /**
     * @param TeamMembership $a
     * @param TeamMembership $b
     *
     * @return bool
     */
    private function sortTeamMembershipsByEndDate($a, $b)
    {
        return $a->getStartSemester()->getStartDate() < $b->getStartSemester()->getStartDate();
    }

    public function updateTeamAction(Request $request, Team $team)
    {
        // Find the department of the team
        $department   = $team->getDepartment();
        $oldTeamEmail = $team->getEmail();

        // Create the form
        $form = $this->createForm(CreateTeamType::class, $team);

        // Handle the form
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Don't persist if the preview button was clicked
            if (! $form->get('preview')->isClicked()) {
                // Persist the team to the database
                $em = $this->getDoctrine()->getManager();
                $em->persist($team);
                $em->flush();

                $this->get('event_dispatcher')->dispatch(TeamEvent::EDITED, new TeamEvent($team, $oldTeamEmail));

                return $this->redirect($this->generateUrl('teamadmin_show'));
            }
            $teamMemberships = $this->getDoctrine()->getRepository('AppBundle:TeamMembership')->findActiveTeamMembershipsByTeam($team);

            // Render the teampage as a preview
            return $this->render('team/team_page.html.twig', array(
                'team'            => $team,
                'teamMemberships' => $teamMemberships,
            ));
        }

        return $this->render('team_admin/create_team.html.twig', array(
            'team'       => $team,
            'department' => $department,
            'form'       => $form->createView(),
            'isUpdate'   => true,
        ));
    }

    public function showTeamsByDepartmentAction(Department $department)
    {
        // Find teams that are connected to the department of the department ID sent in by the request
        $teams = $this->getDoctrine()->getRepository('AppBundle:Team')->findByDepartment($department);

        // Return the view with suitable variables
        return $this->render('team_admin/index.html.twig', array(
            'department' => $department,
            'teams'      => $teams,
        ));
    }

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

        if ($form->isValid()) {
            //Don't persist if the preview button was clicked
            if (! $form->get('preview')->isClicked()) {
                // Persist the team to the database
                $em = $this->getDoctrine()->getManager();
                $em->persist($team);
                $em->flush();

                $this->get('event_dispatcher')->dispatch(TeamEvent::CREATED, new TeamEvent($team, $team->getEmail()));

                return $this->redirect($this->generateUrl('teamadmin_show'));
            }

            // Render the teampage as a preview
            return $this->render('team/team_page.html.twig', array(
                'team'            => $team,
                'teamMemberships' => [],
            ));
        }

        return $this->render('team_admin/create_team.html.twig', array(
            'form'       => $form->createView(),
            'department' => $department,
            'team' => $team,
            'isUpdate' => false
        ));
    }

    public function removeUserFromTeamByIdAction(TeamMembership $teamMembership)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($teamMembership);
        $em->flush();

        $this->get('event_dispatcher')->dispatch(TeamMembershipEvent::DELETED, new TeamMembershipEvent($teamMembership));

        return $this->redirectToRoute('teamadmin_show_specific_team', [ 'id' => $teamMembership->getTeam()->getId() ]);
    }

    public function deleteTeamByIdAction(Team $team)
    {
        $em = $this->getDoctrine()->getManager();

        foreach ($team->getTeamMemberships() as $teamMembership) {
            $teamMembership->setDeletedTeamName($team->getName());
            $em->persist($teamMembership);
        }

        $em->remove($team);
        $em->flush();

        return $this->redirectToRoute("teamadmin_show", [ "id" => $team->getDepartment()->getId() ]);
    }
}
