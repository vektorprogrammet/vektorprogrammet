<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Team;
use AppBundle\Form\Type\CreateTeamType;
use AppBundle\Entity\WorkHistory;
use AppBundle\Form\Type\CreateWorkHistoryType;
use AppBundle\Entity\Position;
use AppBundle\Form\Type\CreatePositionType;

class TeamAdminController extends Controller
{
    public function removePositionAction(Request $request)
    {
        $id = $request->get('id');

        try {

            // This deletes the given position
            $em = $this->getDoctrine()->getEntityManager();
            $position = $this->getDoctrine()->getRepository('AppBundle:Position')->find($id);
            $em->remove($position);
            $em->flush();

            $response['success'] = true;
        } catch (\Exception $e) {
            // Send a response back to AJAX
            $response['success'] = false;
            $response['cause'] = $e->getMessage();

            return new JsonResponse($response);
        }

        // Send a respons to ajax
        return new JsonResponse($response);
    }

    public function editPositionAction(Request $request)
    {

        // Only edit if it is a SUPER_ADMIN
        if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {

            // Get the ID variable from the request
            $id = $request->get('id');

            // Create a new Team entity
            $position = new Position();

            $em = $this->getDoctrine()->getManager();

            // Find a position by the ID sent in by the request
            $position = $em->getRepository('AppBundle:Position')->find($id);

            // Create the form
            $form = $this->createForm(new CreatePositionType(), $position);

            // Handle the form
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->persist($position);
                $em->flush();

                return $this->redirect($this->generateUrl('teamadmin_show_position'));
            }

            return $this->render('team_admin/create_position.html.twig', array(
                 'form' => $form->createView(),
            ));
        } else {
            return $this->redirect($this->generateUrl('home'));
        }
    }

    public function showPositionsAction(Request $request)
    {

        // Find all the positions
        $positions = $this->getDoctrine()->getRepository('AppBundle:Position')->findAll();

        // Return the view with suitable variables
        return $this->render('team_admin/show_positions.html.twig', array(
            'positions' => $positions,
        ));
    }

    public function createPositionAction(Request $request)
    {

        // A new forum entity
        $position = new Position();

        // Create the form
        $form = $this->createForm(new CreatePositionType(), $position);

        // Handle the form
        $form->handleRequest($request);

        // Check if the form is valid
        if ($form->isValid()) {

            // Store the forum in the database
            $em = $this->getDoctrine()->getManager();
            $em->persist($position);
            $em->flush();

            // Redirect to the proper subforum
            return $this->redirect($this->generateUrl('teamadmin_show_position'));
        }

        // Return the view
        return $this->render('team_admin/create_position.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function showAction()
    {

        // Finds all the departments
        $allDepartments = $this->getDoctrine()->getRepository('AppBundle:Department')->findAll();

        // Finds the department for the current logged in user
        $department = $this->get('security.token_storage')->getToken()->getUser()->getFieldOfStudy()->getDepartment();

        // Find teams that are connected to the department of the user
        $teams = $this->getDoctrine()->getRepository('AppBundle:Team')->findByDepartment($department);

        // Return the view with suitable variables
        return $this->render('team_admin/index.html.twig', array(
            'departments' => $allDepartments,
            'teams' => $teams,
            'departmentName' => $department->getShortName(),
        ));
    }

    public function updateWorkHistoryAction(Request $request)
    {

        // Find the id variable sent by the request
        $id = $request->get('id');

        // Create find a WorkHistory entity with the ID given by the request
        $workHistory = $this->getDoctrine()->getRepository('AppBundle:WorkHistory')->find($id);

        // Find the department of the team
        $department = $workHistory->getTeam()->getDepartment();

        // Create a new formType with the needed variables
        $form = $this->createForm(new CreateWorkHistoryType($department), $workHistory);

        // Handle the form
        $form->handleRequest($request);
        if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
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
        } else {
            return $this->redirect($this->generateUrl('home'));
        }
    }

    public function addUserToTeamAction(Request $request)
    {
        if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            // Find the id variable sent by the request
            $id = $request->get('id');

            // Find the team with the given ID
            $team = $this->getDoctrine()->getRepository('AppBundle:Team')->find($id);
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

                return $this->redirect($this->generateUrl('teamadmin_show_specific_team', array('id' => $id)));
            }

            return $this->render('team_admin/create_work_history.html.twig', array(
                'form' => $form->createView(),
            ));
        } else {
            return $this->redirect($this->generateUrl('home'));
        }
    }

    public function showSpecificTeamAction(Request $request)
    {

        // Get the ID variable from the request
        $id = $request->get('id');

        // Find team with the sent ID
        $team = $this->getDoctrine()->getRepository('AppBundle:Team')->find($id);

        // Find all WorkHistory entities based on team
        $activeWorkHistories = $this->getDoctrine()->getRepository('AppBundle:WorkHistory')->findActiveWorkHistoriesByTeam($team);
        $inActiveWorkHistories = $this->getDoctrine()->getRepository('AppBundle:WorkHistory')->findInActiveWorkHistoriesByTeam($team);
        usort($activeWorkHistories, array($this, 'sortWorkHistoriesByEndDate'));
        usort($inActiveWorkHistories, array($this, 'sortWorkHistoriesByEndDate'));

        $user = $this->getUser();
        dump($user);
        $currentUserWorkHistory = $this->getDoctrine()->getRepository('AppBundle:WorkHistory')->findActiveWorkHistoriesByUser($user);
        $isUserInTeam = false;
        dump($currentUserWorkHistory);
        foreach ($currentUserWorkHistory as $wh){
            if (in_array($wh, $activeWorkHistories)) {
                $isUserInTeam = true;
            }
        }
        dump($isUserInTeam);

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

    public function updateTeamAction(request $request)
    {

        // Get the ID variable from the request
        $id = $request->get('id');

        $em = $this->getDoctrine()->getManager();

        // Find a team by the ID sent in by the request
        $team = $em->getRepository('AppBundle:Team')->find($id);

        // Find the department of the team
        $department = $team->getDepartment();

        // Create the form
        $form = $this->createForm(new CreateTeamType($department), $team);

        // Handle the form
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            //Don't persist if the preview button was clicked
            if (false === $form->get('preview')->isClicked()) {
                // Persist the team to the database
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

    public function showTeamsByDepartmentAction(request $request)
    {
        if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            // Find the id variable sent by the request
            $id = $request->get('id');

            // Finds all the departments
            $allDepartments = $this->getDoctrine()->getRepository('AppBundle:Department')->findAll();

            // Find the department for the given ID
            $department = $this->getDoctrine()->getRepository('AppBundle:Department')->findOneById($id);

            // Find teams that are connected to the department of the department ID sent in by the request
            $teams = $this->getDoctrine()->getRepository('AppBundle:Team')->findByDepartment($department);

            // Return the view with suitable variables
            return $this->render('team_admin/index.html.twig', array(
                'departments' => $allDepartments,
                'userDepartment' => $department,
                'teams' => $teams,
                'departmentName' => $department->getShortName(),
            ));
        } else {
            return $this->redirect($this->generateUrl('home'));
        }
    }

    public function createTeamForDepartmentAction(Request $request)
    {
        if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            // Find the id variable sent by the request
            $id = $request->get('id');

            // Find the department for the given ID
            $department = $this->getDoctrine()->getRepository('AppBundle:Department')->findOneById($id);

            // Create a new Team entity
            $team = new Team();

            // Create a new formType with the needed variables
            $form = $this->createForm(new CreateTeamType($department), $team);

            // Handle the form
            $form->handleRequest($request);

            if ($form->isValid()) {

                // Set the teams department to the department sent in by the request
                $team->setDepartment($department);
                $em = $this->getDoctrine()->getManager();
                //Don't persist if the preview button was clicked
                if (false === $form->get('preview')->isClicked()) {
                    // Persist the team to the database
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
        } else {
            return $this->redirect($this->generateUrl('home'));
        }
    }

    public function removeUserFromTeamByIdAction(Request $request)
    {
        $id = $request->get('id');

        try {
            if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {

                // This deletes the given work history
                $em = $this->getDoctrine()->getEntityManager();
                $workHistory = $this->getDoctrine()->getRepository('AppBundle:WorkHistory')->find($id);
                $em->remove($workHistory);
                $em->flush();

                $response['success'] = true;
            } else {
                // Send a response back to AJAX
                $response['success'] = false;
                $response['cause'] = 'Du har ikke tilstrekkelige rettigheter.';
            }
        } catch (\Exception $e) {
            // Send a response back to AJAX
            $response['success'] = false;
            $response['cause'] = 'Kunne ikke slette team historien';

            return new JsonResponse($response);
        }

        // Send a respons to ajax
        return new JsonResponse($response);
    }

    public function deleteTeamByIdAction(request $request)
    {
        $id = $request->get('id');

        try {
            if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {

                // This deletes the given team
                $em = $this->getDoctrine()->getEntityManager();
                $team = $this->getDoctrine()->getRepository('AppBundle:Team')->find($id);
                $em->remove($team);
                $em->flush();

                $response['success'] = true;
            } else {
                // Send a response back to AJAX
                $response['success'] = false;
                $response['cause'] = 'Du har ikke tilstrekkelige rettigheter.';
            }
        } catch (\Exception $e) {
            // Send a response back to AJAX
            $response['success'] = false;
            $response['cause'] = 'Kunne ikke slette team';

            return new JsonResponse($response);
        }

        // Send a respons to ajax
        return new JsonResponse($response);
    }
}
