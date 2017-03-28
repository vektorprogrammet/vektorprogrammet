<?php

namespace AppBundle\Controller;

use AppBundle\Event\WorkHistoryCreatedEvent;
use AppBundle\Form\Type\CreateExecutiveBoardType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\WorkHistory;
use AppBundle\Form\Type\CreateWorkHistoryType;
use AppBundle\Form\Type\CreateExecutiveBoardHistoryType;
use AppBundle\Entity\ExecutiveBoard;
use AppBundle\Entity\Department;

class ExecutiveBoardController extends Controller
{
    public function showAction()
    {
        $board = $this->getDoctrine()->getRepository('AppBundle:ExecutiveBoard')->find(1);
        $workHistories = $this->getDoctrine()->getRepository('AppBundle:WorkHistory')->findActiveWorkHistoriesByTeam($board);

        $leaders = array();
        $members = array();
        foreach ($workHistories as $workHistory) {
            $position = strtolower($workHistory->getPosition());
            if ($position === 'leder' || $position === 'nestleder') {
                $leaders[] = $workHistory;
            } else {
                $members[] = $workHistory;
            }
        }

        usort($members, function (WorkHistory $a, WorkHistory $b) {
            return strcmp(strtolower($a->getPosition()), strtolower($b->getPosition()));
        });

        usort($leaders, function (WorkHistory $a, WorkHistory $b) {
            return strcmp(strtolower($a->getPosition()), strtolower($b->getPosition()));
        });

        return $this->render('executive_board/executive_board_page.html.twig', array(
            'board' => $board,
            'workHistories' => array_merge($leaders, $members),
        ));
    }

    public function showAdminAction()
    {
        $board = $this->getDoctrine()->getRepository('AppBundle:ExecutiveBoard')->find(1);
        return $this->render(':executive_board:index.html.twig', array(
            'board' => $board,
        ));
    }

    public function addUserToBoardAction(Request $request, ExecutiveBoard $board)
    {
        // Create a new WorkHistory entity
        $workHistory = new WorkHistory();
        $allDepartments = $this->getDoctrine()->getRepository('AppBundle:Department')->findAll();

        $getDepartmentId = function(Department $department) {
            return $department->getId();
        };

        $allDepartments = array_map($getDepartmentId, $allDepartments);
        dump($allDepartments);
        // Create a new formType with the needed variables
        $form = $this->createForm(new CreateExecutiveBoardHistoryType(1), $workHistory);

        // Handle the form
        $form->handleRequest($request);

        if ($form->isValid()) {

            $workHistory->setBoard($board);

            // Persist the board to the database
            $em = $this->getDoctrine()->getManager();
            $em->persist($workHistory);
            $em->flush();

            $this->get('event_dispatcher')->dispatch(WorkHistoryCreatedEvent::NAME, new WorkHistoryCreatedEvent($workHistory));

            return $this->redirect($this->generateUrl('executive_board_page', array('id' => $board->getId())));
        }

        return $this->render('executive_board/create_work_history.html.twig', array(
            'form' => $form->createView(),
        ));
    }



    public function removeUserFromBoardByIdAction(WorkHistory $workHistory)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($workHistory);
        $em->flush();

        return new JsonResponse(array(
            'success' => true,
        ));
    }

    public function updateBoardAction(Request $request, ExecutiveBoard $board)
    {
        // Create the form
        $form = $this->createForm(new CreateExecutiveBoardType($board));

        // Handle the form
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Don't persist if the preview button was clicked
            if (!$form->get('preview')->isClicked()) {
                // Persist the board to the database
                $em = $this->getDoctrine()->getManager();
                $em->persist($board);
                $em->flush();

                return $this->redirect($this->generateUrl('executive_board_update'));
            }
              $workHistories = $this->getDoctrine()->getRepository('AppBundle:WorkHistory')->findActiveWorkHistoriesByBoard($board);
            // Render the boardpage as a preview
            return $this->render('executive_board/executive_board_page.html.twig', array(
                'board' => $board,
                'workHistories' => $workHistories,
            ));
        }

        return $this->render('executive_board/executive_board_page.html.twig', array(
            'form' => $form->createView(),
            'isUpdate' => true,
        ));
    }

}
