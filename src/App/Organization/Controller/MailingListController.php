<?php

namespace App\Organization\Controller;

use App\Support\Controller\BaseController;
use App\Organization\Infrastructure\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Entity\Repository\UserRepository;
use App\Organization\Form\GenerateMailingListType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

class MailingListController extends BaseController
{
    public function __construct(
        private readonly UserRepository $userRepo,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    /**
     * @return Response
     */
    #[Route('/kontrollpanel/epostlister', name: 'generate_mail_lists', methods: ['GET', 'POST'])]
    public function showAction(Request $request)
    {
        $form = $this->createForm(GenerateMailingListType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $type = $data['type'];
            $semesterID = $data['semester']->getId();
            $departmentID = $data['department']->getId();

            return match ($type) {
                'Assistent' => $this->redirectToRoute('generate_assistant_mail_list', [
                    'department' => $departmentID,
                    'semester' => $semesterID,
                ]),
                'Team' => $this->redirectToRoute('generate_team_mail_list', [
                    'department' => $departmentID,
                    'semester' => $semesterID,
                ]),
                'Alle' => $this->redirectToRoute('generate_all_mail_list', [
                    'department' => $departmentID,
                    'semester' => $semesterID,
                ]),
                default => throw new BadRequestHttpException('type can only be "Assistent", "Team" or "Alle". Was: '.$type),
            };
        }

        return $this->render('mailing_list/generate_mail_list.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return Response
     */
    #[Route('/kontrollpanel/epostlister/assistenter', name: 'generate_assistant_mail_list', methods: ['GET'])]
    public function showAssistantsAction(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);
        $users = $this->userRepo
            ->findUsersWithAssistantHistoryInDepartmentAndSemester($department, $semester);

        return $this->render('mailing_list/mailinglist_show.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @return Response
     */
    #[Route('/kontrollpanel/epostlister/teammedlemmer', name: 'generate_team_mail_list', methods: ['GET'])]
    public function showTeamAction(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);
        $users = $this->userRepo
            ->findUsersInDepartmentWithTeamMembershipInSemester($department, $semester);

        return $this->render('mailing_list/mailinglist_show.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @return Response
     */
    #[Route('/kontrollpanel/epostlister/alle', name: 'generate_all_mail_list', methods: ['GET'])]
    public function showAllAction(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);
        $assistantUsers = $this->userRepo
            ->findUsersWithAssistantHistoryInDepartmentAndSemester($department, $semester);
        $teamUsers = $this->userRepo
            ->findUsersInDepartmentWithTeamMembershipInSemester($department, $semester);
        $users = array_unique(array_merge($assistantUsers, $teamUsers));

        return $this->render('mailing_list/mailinglist_show.html.twig', [
            'users' => $users,
        ]);
    }
}
