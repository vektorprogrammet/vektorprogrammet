<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use AppBundle\Entity\ExecutiveBoard;
use AppBundle\Entity\ExecutiveBoardMembership;
use AppBundle\Form\Type\CreateExecutiveBoardMembershipType;
use AppBundle\Form\Type\CreateExecutiveBoardType;
use AppBundle\Repository\Contract\ExecutiveBoardRepositoryInterface;
use AppBundle\Repository\Contract\ExecutiveBoardMembershipRepositoryInterface;
use AppBundle\Service\Contract\ExecutiveBoardServiceInterface;
use AppBundle\Service\Contract\ExecutiveBoardManagementServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExecutiveBoardController extends BaseController
{
    private $entityManager;
    private $executiveBoardRepository;
    private $executiveBoardMembershipRepository;
    private $executiveBoardService;
    private $executiveBoardManagementService;

    /**
     * @param EntityManagerInterface $entityManager
     * @param ExecutiveBoardRepositoryInterface $executiveBoardRepository
     * @param ExecutiveBoardMembershipRepositoryInterface $executiveBoardMembershipRepository
     * @param ExecutiveBoardServiceInterface $executiveBoardService
     * @param ExecutiveBoardManagementServiceInterface $executiveBoardManagementService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ExecutiveBoardRepositoryInterface $executiveBoardRepository,
        ExecutiveBoardMembershipRepositoryInterface $executiveBoardMembershipRepository,
        ExecutiveBoardServiceInterface $executiveBoardService,
        ExecutiveBoardManagementServiceInterface $executiveBoardManagementService
    ) {
        $this->entityManager = $entityManager;
        $this->executiveBoardRepository = $executiveBoardRepository;
        $this->executiveBoardMembershipRepository = $executiveBoardMembershipRepository;
        $this->executiveBoardService = $executiveBoardService;
        $this->executiveBoardManagementService = $executiveBoardManagementService;
    }
    public function showAction()
    {
        $board = $this->executiveBoardRepository->findBoard();

        return $this->render('team/team_page.html.twig', array(
            'team'  => $board,
        ));
    }

    public function showAdminAction()
    {
        $board = $this->executiveBoardRepository->findBoard();
        $members = $this->executiveBoardMembershipRepository->findAll();
        
        $separatedMembers = $this->executiveBoardService->separateMembersByStatus($members);

        return $this->render(':executive_board:index.html.twig', array(
            'board_name' => $board->getName(),
            'active_members' => $separatedMembers['active'],
            'inactive_members' => $separatedMembers['inactive'],
        ));
    }

    public function addUserToBoardAction(Request $request, Department $department)
    {
        $board = $this->entityManager->getRepository(ExecutiveBoard::class)->findBoard();

        // Create a new TeamMembership entity
        $member = new ExecutiveBoardMembership();
        $member->setUser($this->getUser());

        // Create a new formType with the needed variables
        $form = $this->createForm(CreateExecutiveBoardMembershipType::class, $member, [
            'departmentId' => $department
        ]);

        // Handle the form
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->executiveBoardManagementService->createMembership($member, $board, $this->getUser());

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
        $this->executiveBoardManagementService->removeMembership($member);

        return $this->redirect($this->generateUrl('executive_board_show'));
    }

    public function updateBoardAction(Request $request)
    {
        $board = $this->executiveBoardRepository->findBoard();

        // Create the form
        $form = $this->createForm(CreateExecutiveBoardType::class, $board);

        // Handle the form
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //Don't persist if the preview button was clicked
            if (!$form->get('preview')->isClicked()) {
                $this->executiveBoardManagementService->updateBoard($board);

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
     * @Route("/kontrollpanel/hovedstyret/rediger_medlem/{id}",
     *     name="edit_executive_board_membership",
     *     requirements={"id"="\d+"},
     *     methods={"GET", "POST"}
     * )
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
            $this->executiveBoardManagementService->updateMembership($member);
            return $this->redirectToRoute('executive_board_show');
        }

        $memberName = $user->getFullName();
        return $this->render("executive_board/member.html.twig", array(
            'heading' => "Rediger medlemshistorikken til $memberName",
            'form' => $form->createView(),
        ));
    }
}
