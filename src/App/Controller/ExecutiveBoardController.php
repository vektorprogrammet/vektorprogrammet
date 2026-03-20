<?php

namespace App\Controller;

use App\Entity\Department;
use App\Entity\ExecutiveBoardMembership;
use App\Entity\Repository\DepartmentRepository;
use App\Entity\Repository\ExecutiveBoardMembershipRepository;
use App\Entity\Repository\ExecutiveBoardRepository;
use App\Shared\Repository\SemesterRepository;
use App\Form\Type\CreateExecutiveBoardMembershipType;
use App\Form\Type\CreateExecutiveBoardType;
use App\Service\RoleManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ExecutiveBoardController extends BaseController
{
    public function __construct(
        private readonly ExecutiveBoardRepository $executiveBoardRepo,
        private readonly ExecutiveBoardMembershipRepository $executiveBoardMembershipRepo,
        private readonly RoleManager $roleManager,
        private readonly EntityManagerInterface $em,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    #[Route('/hovedstyret', name: 'executive_board_page', methods: ['GET'])]
    public function showAction()
    {
        $board = $this->executiveBoardRepo->findBoard();

        return $this->render('team/team_page.html.twig', [
            'team' => $board,
        ]);
    }

    #[Route('/kontrollpanel/hovedstyret', name: 'executive_board_show', methods: ['GET'])]
    public function showAdminAction()
    {
        $board = $this->executiveBoardRepo->findBoard();
        $members = $this->executiveBoardMembershipRepo->findAll();
        $activeMembers = [];
        $inactiveMembers = [];
        foreach ($members as $member) {
            if ($member->isActive()) {
                $activeMembers[] = $member;
            } else {
                $inactiveMembers[] = $member;
            }
        }

        return $this->render('executive_board/index.html.twig', [
            'board_name' => $board->getName(),
            'active_members' => $activeMembers,
            'inactive_members' => $inactiveMembers,
        ]);
    }

    #[Route('/kontrollpanel/hovedstyret/nytt_medlem/{id}', name: 'executive_board_add_user_to_board', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function addUserToBoardAction(Request $request, Department $department)
    {
        $board = $this->executiveBoardRepo->findBoard();

        // Create a new TeamMembership entity
        $member = new ExecutiveBoardMembership();
        $member->setUser($this->getUser());

        // Create a new formType with the needed variables
        $form = $this->createForm(CreateExecutiveBoardMembershipType::class, $member, [
            'departmentId' => $department,
        ]);

        // Handle the form
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $member->setBoard($board);

            // Persist the board to the database
            $this->em->persist($member);
            $this->em->flush();

            $this->roleManager->updateUserRole($member->getUser());

            return $this->redirect($this->generateUrl('executive_board_show'));
        }

        $city = $department->getCity();

        return $this->render('executive_board/member.html.twig', [
            'heading' => "Legg til hovedstyremedlem fra avdeling $city",
            'form' => $form->createView(),
        ]);
    }

    #[Route('/kontrollpanel/hovedstyret/slett/bruker/{id}', name: 'executive_board_remove_user_from_board_by_id', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function removeUserFromBoardByIdAction(ExecutiveBoardMembership $member)
    {
        $this->em->remove($member);
        $this->em->flush();

        $this->roleManager->updateUserRole($member->getUser());

        return $this->redirect($this->generateUrl('executive_board_show'));
    }

    #[Route('/kontrollpanel/hovedstyret/oppdater', name: 'executive_board_update', methods: ['GET', 'POST'])]
    public function updateBoardAction(Request $request)
    {
        $board = $this->executiveBoardRepo->findBoard();

        // Create the form
        $form = $this->createForm(CreateExecutiveBoardType::class, $board);

        // Handle the form
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Don't persist if the preview button was clicked
            if (!$form->get('preview')->isClicked()) {
                // Persist the board to the database
                $this->em->persist($board);
                $this->em->flush();

                return $this->redirect($this->generateUrl('executive_board_show'));
            }

            // Render the boardpage as a preview
            return $this->render('team/team_page.html.twig', [
                'team' => $board,
                'teamMemberships' => $board->getBoardMemberships(),
            ]);
        }

        return $this->render('executive_board/update_executive_board.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return Response
     */
    #[Route('/kontrollpanel/hovedstyret/rediger_medlem/{id}', name: 'edit_executive_board_membership', requirements: ['id' => "\d+"], methods: ['GET', 'POST'])]
    public function editMemberHistoryAction(Request $request, ExecutiveBoardMembership $member)
    {
        $user = $member->getUser(); // Store the $user object before the form touches our $member object with spooky user data
        $form = $this->createForm(CreateExecutiveBoardMembershipType::class, $member, [
            'departmentId' => $user->getDepartment(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($member);
            $this->em->flush();

            return $this->redirectToRoute('executive_board_show');
        }

        $memberName = $user->getFullName();

        return $this->render('executive_board/member.html.twig', [
            'heading' => "Rediger medlemshistorikken til $memberName",
            'form' => $form->createView(),
        ]);
    }
}
