<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use AppBundle\Service\RoleManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\CreateExecutiveBoardType;
use AppBundle\Form\Type\CreateExecutiveBoardMembershipType;
use AppBundle\Entity\ExecutiveBoardMembership;
use Symfony\Component\HttpFoundation\Response;

class ExecutiveBoardController extends BaseController
{
    public function showAction()
    {
        $board = $this->getDoctrine()->getRepository('AppBundle:ExecutiveBoard')->findBoard();

        return $this->render('team/team_page.html.twig', array(
            'team'  => $board,
        ));
    }

    public function showAdminAction()
    {
        $board = $this->getDoctrine()->getRepository('AppBundle:ExecutiveBoard')->findBoard();
        $members = $this->getDoctrine()->getRepository('AppBundle:ExecutiveBoardMembership')->findAll();
        $activeMembers = [];
        $inactiveMembers = [];
        foreach ($members as $member) {
            if ($member->isActive()) {
                $activeMembers[] = $member;
            } else {
                $inactiveMembers[] = $member;
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
        $member = new ExecutiveBoardMembership();
        $member->setUser($this->getUser());

        // Create a new formType with the needed variables
        $form = $this->createForm(CreateExecutiveBoardMembershipType::class, $member, [
            'departmentId' => $department
        ]);

        // Handle the form
        $form->handleRequest($request);

        if ($form->isValid()) {
            $member->setBoard($board);

            // Persist the board to the database
            $em = $this->getDoctrine()->getManager();
            $em->persist($member);
            $em->flush();

            $this->get(RoleManager::class)->updateUserRole($member->getUser());

            return $this->redirect($this->generateUrl('executive_board_show'));
        }

        $city = $department->getCity();
        return $this->render('executive_board/member.html.twig', array(
            'heading' => "Legg til hovedstyremedlem fra avdeling $city",
            'form' => $form->createView(),
        ));
    }

    public function removeUserFromBoardByIdAction(ExecutiveBoardMembership $member)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($member);
        $em->flush();

        $this->get(RoleManager::class)->updateUserRole($member->getUser());

        return $this->redirect($this->generateUrl('executive_board_show'));
    }

    public function updateBoardAction(Request $request)
    {
        $board = $this->getDoctrine()->getRepository('AppBundle:ExecutiveBoard')->findBoard();

        // Create the form
        $form = $this->createForm(CreateExecutiveBoardType::class, $board);

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
                'teamMemberships' => $board->getBoardMemberships(),
            ));
        }

        return $this->render('executive_board/update_executive_board.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/kontrollpanel/hovedstyret/rediger_medlem/{id}", name="edit_executive_board_membership", requirements={"id"="\d+"})
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param ExecutiveBoardMembership $member
     *
     * @return Response
     */
    public function editMemberHistoryAction(Request $request, ExecutiveBoardMembership $member)
    {
        $user = $member->getUser(); // Store the $user object before the form touches our $member object with spooky user data
        $form = $this->createForm(CreateExecutiveBoardMembershipType::class, $member, [
            'departmentId' => $user->getDepartment()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($member);
            $em->flush();
            return $this->redirectToRoute('executive_board_show');
        }

        $memberName = $user->getFullName();
        return $this->render("executive_board/member.html.twig", array(
            'heading' => "Rediger medlemshistorikken til $memberName",
            'form' => $form->createView(),
        ));
    }
}
