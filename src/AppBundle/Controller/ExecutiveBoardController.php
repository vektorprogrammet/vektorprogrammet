<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\CreateExecutiveBoardType;
use AppBundle\Form\Type\CreateExecutiveBoardMemberType;
use AppBundle\Entity\ExecutiveBoard;
use AppBundle\Entity\ExecutiveBoardMember;
use Symfony\Component\HttpFoundation\Response;

class ExecutiveBoardController extends Controller
{
    public function showAction()
    {
        $board = $this->getDoctrine()->getRepository('AppBundle:ExecutiveBoard')->findBoard();

        $sorter = $this->get('app.sorter');

        $users = $board->getActiveUsers();
        $sorter->sortUsersByActiveExecutiveBoardPosition($users);

        // Sort all the users' executive board histories by position
        // (So for example "Leder" comes before "Sekretær" if the user has multiple positions)
        foreach ($users as $user) {
            $executiveBoardMembers = $user->getActiveExecutiveBoardMembers();
            $sorter->sortTeamMembershipsByPosition($executiveBoardMembers);
            $user->setExecutiveBoardMembers($executiveBoardMembers);
        }

        return $this->render('team/team_page.html.twig', array(
            'team'  => $board,
            'users' => $users,
            'type' => 'board',
        ));
    }

    public function showAdminAction()
    {
        $board = $this->getDoctrine()->getRepository('AppBundle:ExecutiveBoard')->findBoard();
        $members = $this->getDoctrine()->getRepository('AppBundle:ExecutiveBoardMember')->findAll();
        $activeMembers = [];
        $inactiveMembers = [];
        foreach ($members as $member) {
            if ($member->isActive()) {
                array_push($activeMembers, $member);
            } else {
                array_push($inactiveMembers, $member);
            }
        }

        return $this->render(':executive_board:index.html.twig', array(
            'board_name' => $board->getName(),
            'active_members' => $activeMembers,
            'inactive_members' => $inactiveMembers,
        ));
    }

    public function addUserToBoardAction(Request $request, Department $department)
    {
        $board = $this->getDoctrine()->getRepository('AppBundle:ExecutiveBoard')->findBoard();

        // Create a new TeamMembership entity
        $member = new ExecutiveBoardMember();
        $member->setUser($this->getUser());

        // Create a new formType with the needed variables
        $form = $this->createForm(new CreateExecutiveBoardMemberType($department), $member);

        // Handle the form
        $form->handleRequest($request);

        if ($form->isValid()) {
            $member->setBoard($board);

            // Persist the board to the database
            $em = $this->getDoctrine()->getManager();
            $em->persist($member);
            $em->flush();

            $this->get('app.roles')->updateUserRole($member->getUser());

            return $this->redirect($this->generateUrl('executive_board_show'));
        }

        $city = $department->getCity();
        return $this->render('executive_board/member.html.twig', array(
            'heading' => "Legg til hovedstyremedlem fra avdeling $city",
            'form' => $form->createView(),
        ));
    }

    public function removeUserFromBoardByIdAction(ExecutiveBoardMember $member)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($member);
        $em->flush();

        $this->get('app.roles')->updateUserRole($member->getUser());

        return $this->redirect($this->generateUrl('executive_board_show'));
    }

    public function updateBoardAction(Request $request)
    {
        $board = $this->getDoctrine()->getRepository('AppBundle:ExecutiveBoard')->findBoard();

        // Create the form
        $form = $this->createForm(new CreateExecutiveBoardType($board), $board);

        // Handle the form
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //Don't persist if the preview button was clicked
            if (!$form->get('preview')->isClicked()) {
                // Persist the board to the database
                $em = $this->getDoctrine()->getManager();
                $em->persist($board);
                $em->flush();

                return $this->redirect($this->generateUrl('executive_board_show'));
            }

            // Render the boardpage as a preview
            return $this->render('team/team_page.html.twig', array(
                'team' => $board,
                'teamMemberships' => $board->getMembers(),
            ));
        }

        return $this->render('executive_board/update_executive_board.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/kontrollpanel/hovedstyret/rediger_medlem/{id}", name="edit_executive_board_member_history", requirements={"id"="\d+"})
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param ExecutiveBoardMember $member
     *
     * @return Response
     */
    public function editMemberHistory(Request $request, ExecutiveBoardMember $member)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new CreateExecutiveBoardMemberType($member->getUser()->getDepartment()), $member);
        $user = $member->getUser();
        $form->handleRequest($request); // Sets user to null
        $member->setUser($user);

        if ($form->isValid()) {
            $em->persist($member);
            $em->flush();
            return $this->redirectToRoute('executive_board_show');
        }

        $form->remove('user');
        $memberName = $member->getUser()->getFullName();
        return $this->render("executive_board/member.html.twig", array(
            'heading' => "Rediger medlemshistorikken til $memberName",
            'form' => $form->createView(),
        ));
    }
}
