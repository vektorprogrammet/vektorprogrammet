<?php

namespace App\Controller;

use App\Entity\Position;
use App\Entity\Repository\DepartmentRepository;
use App\Entity\Repository\PositionRepository;
use App\Entity\Repository\SemesterRepository;
use App\Form\Type\CreatePositionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class PositionController extends BaseController
{
    public function __construct(
        private PositionRepository $positionRepo,
        private EntityManagerInterface $em,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    public function showPositionsAction()
    {
        // Find all the positions
        $positions = $this->positionRepo->findAll();

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

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($position);
            $this->em->flush();

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
        $this->em->remove($position);
        $this->em->flush();

        $this->addFlash("success", "Stillingen ble slettet.");

        return $this->redirectToRoute("teamadmin_show_position");
    }
}
