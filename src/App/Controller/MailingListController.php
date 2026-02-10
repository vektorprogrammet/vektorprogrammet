<?php

namespace App\Controller;

use App\Entity\Repository\DepartmentRepository;
use App\Entity\Repository\SemesterRepository;
use App\Entity\Repository\UserRepository;
use App\Entity\User;
use App\Form\Type\GenerateMailingListType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class MailingListController extends BaseController
{
    public function __construct(
        private UserRepository $userRepo,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function showAction(Request $request)
    {
        $form = $this->createForm(GenerateMailingListType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $type = $data['type'];
            $semesterID = $data['semester']->getId();
            $departmentID = $data['department']->getId();

            switch ($type) {
                case 'Assistent':
                    return $this->redirectToRoute('generate_assistant_mail_list', array(
                        'department' => $departmentID,
                        'semester' => $semesterID,
                    ));
                case 'Team':
                    return $this->redirectToRoute('generate_team_mail_list', array(
                        'department' => $departmentID,
                        'semester' => $semesterID,
                    ));
                case 'Alle':
                    return $this->redirectToRoute('generate_all_mail_list', array(
                        'department' => $departmentID,
                        'semester' => $semesterID,
                    ));
                default:
                    throw new BadRequestHttpException('type can only be "Assistent", "Team" or "Alle". Was: '.$type);
            }
        }

        return $this->render('mailing_list/generate_mail_list.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function showAssistantsAction(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);
        $users = $this->userRepo
            ->findUsersWithAssistantHistoryInDepartmentAndSemester($department, $semester);

        return $this->render('mailing_list/mailinglist_show.html.twig', array(
            'users' => $users,
        ));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function showTeamAction(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);
        $users = $this->userRepo
            ->findUsersInDepartmentWithTeamMembershipInSemester($department, $semester);

        return $this->render('mailing_list/mailinglist_show.html.twig', array(
            'users' => $users,
        ));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function showAllAction(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);
        $assistantUsers = $this->userRepo
            ->findUsersWithAssistantHistoryInDepartmentAndSemester($department, $semester);
        $teamUsers = $this->userRepo
            ->findUsersInDepartmentWithTeamMembershipInSemester($department, $semester);
        $users = array_unique(array_merge($assistantUsers, $teamUsers));

        return $this->render('mailing_list/mailinglist_show.html.twig', array(
            'users' => $users,
        ));
    }
}
