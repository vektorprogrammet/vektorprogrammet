<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Position;
use AppBundle\Form\Type\CreatePositionType;

class PositionController extends Controller
{
    public function showPositionsAction()
    {
        // Find all the positions
        $positions = $this->getDoctrine()->getRepository('AppBundle:Position')->findAll();

        // Return the view with suitable variables
        return $this->render('team_admin/show_positions.html.twig', array(
            'positions' => $positions,
        ));
    }

    public function editPositionAction(Request $request, Position $position = null)
    {
        if ($position === null) {
            $position = new Position();
        }

        $form = $this->createForm(CreatePositionType::class, $position);

        // Handle the form
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($position);
            $em->flush();

            return $this->redirectToRoute('teamadmin_show_position');
        }

        return $this->render('team_admin/create_position.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function removePositionAction(Position $position)
    {
        try {
            // This deletes the given position
            $em = $this->getDoctrine()->getManager();
            $em->remove($position);
            $em->flush();

            return new JsonResponse(array(
                'success' => true,
            ));
        } catch (\Exception $e) {
            // Send a response back to AJAX
            return new JsonResponse(array(
                'success' => true,
                'cause' => $e->getMessage(),
            ));
        }
    }
}
