<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\CreateExecutiveBoardType;
use AppBundle\Form\Type\CreateExecutiveBoardMemberType;
use AppBundle\Entity\ExecutiveBoard;
use AppBundle\Entity\ExecutiveBoardMember;

class ExecutiveBoardController extends Controller
{
    public function showAction()
    {
        $board = $this->getDoctrine()->getRepository('AppBundle:ExecutiveBoard')->find(1);
        $members = $board->getUsers();

        return $this->render('executive_board/executive_board_page.html.twig', array(
            'board' => $board,
            'members' => $members,
        ));
    }

    public function showAdminAction()
    {
        $board = $this->getDoctrine()->getRepository('AppBundle:ExecutiveBoard')->find(1);
        $members = $board->getUsers();
        return $this->render(':executive_board:index.html.twig', array(
            'board' => $board,
            'members' => $members,
        ));
    }

    public function addUserToBoardAction(Request $request, ExecutiveBoard $board)
    {
        // Create a new WorkHistory entity
        $member = new ExecutiveBoardMember();

        // Create a new formType with the needed variables
        $form = $this->createForm(new CreateExecutiveBoardMemberType(), $member);

        // Handle the form
        $form->handleRequest($request);

        if ($form->isValid()) {

            $member->setBoard($board);

            // Persist the board to the database
            $em = $this->getDoctrine()->getManager();
            $em->persist($member);
            $em->flush();

            return $this->redirect($this->generateUrl('executive_board_show'));
        }

        return $this->render('executive_board/create_member.html.twig', array(
            'form' => $form->createView(),
        ));
    }



    public function removeUserFromBoardByIdAction(ExecutiveBoardMember $member)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($member);
        $em->flush();

        return $this->redirect($this->generateUrl('executive_board_show'));
    }

    public function updateBoardAction(Request $request, ExecutiveBoard $board)
    {
        // Create the form
        $form = $this->createForm(new CreateExecutiveBoardType($board), $board);

        // Handle the form
        $form->handleRequest($request);
        $members = $board->getUsers();
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
            return $this->render('executive_board/executive_board_page.html.twig', array(
                'board' => $board,
                'members' => $members,
            ));
        }

        return $this->render('executive_board/update_executive_board.html.twig', array(
            'form' => $form->createView(),
            'isUpdate' => true,
        ));
    }

}
