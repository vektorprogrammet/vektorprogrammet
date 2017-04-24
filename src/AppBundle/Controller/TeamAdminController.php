<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use AppBundle\Event\WorkHistoryCreatedEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Team;
use AppBundle\Form\Type\CreateTeamType;
use AppBundle\Entity\WorkHistory;
use AppBundle\Form\Type\CreateWorkHistoryType;

class TeamAdminController extends Controller
{
    public function showAction()
    {
        // Finds the department for the current logged in user
        $department = $this->getUser()->getDepartment();

        // Find teams that are connected to the department of the user
        $teams = $this->getDoctrine()->getRepository('AppBundle:Team')->findByDepartment($department);

        // Return the view with suitable variables
        return $this->render('team_admin/index.html.twig', array(
            'teams' => $teams,
            'department' => $department,
        ));
    }

    public function updateWorkHistoryAction(Request $request, WorkHistory $workHistory)
    {
        // Find the department of the team
        $department = $workHistory->getTeam()->getDepartment();

        // Create a new formType with the needed variables
        $form = $this->createForm(new CreateWorkHistoryType($department), $workHistory);

        // Handle the form
        $form->handleRequest($request);
        if ($form->isValid()) {
            // Persist the team to the database
            $em = $this->getDoctrine()->getManager();
            $em->persist($workHistory);
            $em->flush();

            return $this->redirect($this->generateUrl('teamadmin_show_specific_team', array('id' => $workHistory->getTeam()->getId())));
        }

        return $this->render('team_admin/create_work_history.html.twig', array(
                'form' => $form->createView(),
            ));
    }

    public function addUserToTeamAction(Request $request, Team $team)
    {
        // Find the department of the team
            $department = $team->getDepartment();

            // Create a new WorkHistory entity
            $workHistory = new WorkHistory();

            // Create a new formType with the needed variables
            $form = $this->createForm(new CreateWorkHistoryType($department), $workHistory);

            // Handle the form
            $form->handleRequest($request);

        if ($form->isValid()) {

            //set the team of the department
            $workHistory->setTeam($team);

            // Persist the team to the database
            $em = $this->getDoctrine()->getManager();
            $em->persist($workHistory);
            $em->flush();

            $this->get('event_dispatcher')->dispatch(WorkHistoryCreatedEvent::NAME, new WorkHistoryCreatedEvent($workHistory));

            return $this->redirect($this->generateUrl('teamadmin_show_specific_team', array('id' => $team->getId())));
        }

        return $this->render('team_admin/create_work_history.html.twig', array(
                'form' => $form->createView(),
            ));
    }

    public function showSpecificTeamAction(Team $team)
    {
        // Find all WorkHistory entities based on team
        $activeWorkHistories = $this->getDoctrine()->getRepository('AppBundle:WorkHistory')->findActiveWorkHistoriesByTeam($team);
        $inActiveWorkHistories = $this->getDoctrine()->getRepository('AppBundle:WorkHistory')->findInActiveWorkHistoriesByTeam($team);
        usort($activeWorkHistories, array($this, 'sortWorkHistoriesByEndDate'));
        usort($inActiveWorkHistories, array($this, 'sortWorkHistoriesByEndDate'));

        $user = $this->getUser();
        $currentUserWorkHistory = $this->getDoctrine()->getRepository('AppBundle:WorkHistory')->findActiveWorkHistoriesByUser($user);
        $isUserInTeam = false;
        foreach ($currentUserWorkHistory as $wh) {
            if (in_array($wh, $activeWorkHistories)) {
                $isUserInTeam = true;
            }
        }

        // Return the view with suitable variables
        return $this->render('team_admin/specific_team.html.twig', array(
            'team' => $team,
            'activeWorkHistories' => $activeWorkHistories,
            'inActiveWorkHistories' => $inActiveWorkHistories,
            'isUserInTeam' => $isUserInTeam,
        ));
    }

    /**
     * @param WorkHistory $a
     * @param WorkHistory $b
     *
     * @return bool
     */
    private function sortWorkHistoriesByEndDate($a, $b)
    {
        return $a->getStartSemester()->getSemesterStartDate() < $b->getStartSemester()->getSemesterStartDate();
    }

    public function updateTeamAction(Request $request, Team $team)
    {
        // Find the department of the team
        $department = $team->getDepartment();

        // Create the form
        $form = $this->createForm(new CreateTeamType($department), $team);

        // Handle the form
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Don't persist if the preview button was clicked
            if (!$form->get('preview')->isClicked()) {
                // Persist the team to the database
                $em = $this->getDoctrine()->getManager();
                $em->persist($team);
                $em->flush();

                return $this->redirect($this->generateUrl('teamadmin_show'));
            }
            $workHistories = $this->getDoctrine()->getRepository('AppBundle:WorkHistory')->findActiveWorkHistoriesByTeam($team);
            // Render the teampage as a preview
            return $this->render('team/team_page.html.twig', array(
                'team' => $team,
                'workHistories' => $workHistories,
            ));
        }

        return $this->render('team_admin/create_team.html.twig', array(
            'department' => $department,
            'form' => $form->createView(),
            'isUpdate' => true,
        ));
    }

    public function showTeamsByDepartmentAction(Department $department)
    {
        // Find teams that are connected to the department of the department ID sent in by the request
        $teams = $this->getDoctrine()->getRepository('AppBundle:Team')->findByDepartment($department);

        // Return the view with suitable variables
        return $this->render('team_admin/index.html.twig', array(
            'department' => $department,
            'teams' => $teams,
        ));
    }

    public function createTeamForDepartmentAction(Request $request, Department $department)
    {
        // Create a new Team entity
        $team = new Team();

        // Create a new formType with the needed variables
        $form = $this->createForm(new CreateTeamType($department), $team);

        // Handle the form
        $form->handleRequest($request);

        if ($form->isValid()) {
            // Set the teams department to the department sent in by the request
            $team->setDepartment($department);
            //Don't persist if the preview button was clicked
            if (!$form->get('preview')->isClicked()) {
                // Persist the team to the database
                $em = $this->getDoctrine()->getManager();
                $em->persist($team);
                $em->flush();

                return $this->redirect($this->generateUrl('teamadmin_show'));
            }
            // Render the teampage as a preview
            return $this->render('team/team_page.html.twig', array(
                'team' => $team,
                'workHistories' => [],
            ));
        }

        return $this->render('team_admin/create_team.html.twig', array(
            'form' => $form->createView(),
            'department' => $department,
        ));
    }

    public function removeUserFromTeamByIdAction(WorkHistory $workHistory)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($workHistory);
        $em->flush();

        return new JsonResponse(array(
            'success' => true,
        ));
    }

    public function deleteTeamByIdAction(Team $team)
    {
        $em = $this->getDoctrine()->getManager();

        foreach ($team->getWorkHistories() as $workHistory) {
            $workHistory->setDeletedTeamName($team->getName());
            $em->persist($workHistory);
        }

        $em->remove($team);
        $em->flush();

        return new JsonResponse(array(
            'success' => true,
        ));
    }
}
