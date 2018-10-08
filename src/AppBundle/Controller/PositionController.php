<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Position;
use AppBundle\Form\Type\CreatePositionType;

class PositionController extends BaseController
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
        $isCreate = $position === null;
        if ($isCreate) {
            $position = new Position();
        }

        $form = $this->createForm(CreatePositionType::class, $position);

        // Handle the form
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($position);
            $em->flush();

            $flash = "Stillingen ble ";
            $flash .= $isCreate ? "opprettet." : "endret.";

            $this->addFlash("success", $flash);

            return $this->redirectToRoute('teamadmin_show_position');
        }

        return $this->render('team_admin/create_position.html.twig', array(
            'form' => $form->createView(),
            'isCreate' => $isCreate,
            'position' => $position
        ));
    }

    public function removePositionAction(Position $position)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($position);
        $em->flush();

        $this->addFlash("success", "Stillingen ble slettet.");

        return $this->redirectToRoute("teamadmin_show_position");
    }
}
