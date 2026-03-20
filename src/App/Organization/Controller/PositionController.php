<?php

declare(strict_types=1);

namespace App\Organization\Controller;

use App\Support\Controller\BaseController;
use App\Organization\Infrastructure\Entity\Position;
use App\Organization\Infrastructure\Repository\DepartmentRepository;
use App\Organization\Infrastructure\Repository\PositionRepository;
use App\Shared\Repository\SemesterRepository;
use App\Organization\Form\CreatePositionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class PositionController extends BaseController
{
    public function __construct(
        private readonly PositionRepository $positionRepo,
        private readonly EntityManagerInterface $em,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    #[Route('/kontrollpanel/teamadmin/stillinger', name: 'teamadmin_show_position', methods: ['GET'])]
    public function showPositionsAction()
    {
        // Find all the positions
        $positions = $this->positionRepo->findAll();

        // Return the view with suitable variables
        return $this->render('team_admin/show_positions.html.twig', [
            'positions' => $positions,
        ]);
    }

    #[Route('/kontrollpanel/teamadmin/opprett/stilling', name: 'teamadmin_create_position', methods: ['GET', 'POST'])]
    #[Route('/kontrollpanel/teamadmin/rediger/stilling/{id}', name: 'teamadmin_edit_position', methods: ['GET', 'POST'])]
    public function editPositionAction(Request $request, ?Position $position = null)
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

            $flash = 'Stillingen ble ';
            $flash .= $isCreate ? 'opprettet.' : 'endret.';

            $this->addFlash('success', $flash);

            return $this->redirectToRoute('teamadmin_show_position');
        }

        return $this->render('team_admin/create_position.html.twig', [
            'form' => $form->createView(),
            'isCreate' => $isCreate,
            'position' => $position,
        ]);
    }

    #[Route('/kontrollpanel/teamadmin/stilling/slett/{id}', name: 'teamadmin_remove_position', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function removePositionAction(Position $position)
    {
        $this->em->remove($position);
        $this->em->flush();

        $this->addFlash('success', 'Stillingen ble slettet.');

        return $this->redirectToRoute('teamadmin_show_position');
    }
}
