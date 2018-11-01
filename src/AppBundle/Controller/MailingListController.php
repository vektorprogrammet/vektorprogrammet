<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use AppBundle\Form\Type\GenerateMailingListType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class MailingListController extends BaseController
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request)
    {
        $form = $this->createForm(GenerateMailingListType::class);
        $form->handleRequest($request);

        if ($form->isValid()) {
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAssistantsAction()
    {
        $department = $this->getDepartmentOrThrow404();
        $semester = $this->getSemesterOrThrow404();
        $users = $this->getDoctrine()->getRepository('AppBundle:User')
            ->findUsersWithAssistantHistoryInDepartmentAndSemester($department, $semester);

        return $this->render('mailing_list/mailinglist_show.html.twig', array(
            'users' => $users,
        ));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showTeamAction()
    {
        $department = $this->getDepartmentOrThrow404();
        $semester = $this->getSemesterOrThrow404();
        $users = $this->getDoctrine()->getRepository('AppBundle:User')
            ->findUsersInDepartmentWithTeamMembershipInSemester($department, $semester);

        return $this->render('mailing_list/mailinglist_show.html.twig', array(
            'users' => $users,
        ));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAllAction()
    {
        $department = $this->getDepartmentOrThrow404();
        $semester = $this->getSemesterOrThrow404();
        $assistantUsers = $this->getDoctrine()->getRepository('AppBundle:User')
            ->findUsersWithAssistantHistoryInDepartmentAndSemester($department, $semester);
        $teamUsers = $this->getDoctrine()->getRepository('AppBundle:User')
            ->findUsersInDepartmentWithTeamMembershipInSemester($department, $semester);
        $users = array_unique(array_merge($assistantUsers, $teamUsers));

        return $this->render('mailing_list/mailinglist_show.html.twig', array(
            'users' => $users,
        ));
    }
}
