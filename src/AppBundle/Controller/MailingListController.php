<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Semester;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\Type\GenerateMailingListType;
use Symfony\Component\HttpFoundation\Request;

class MailingListController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request)
    {
        $semesters = $this->getDoctrine()->getRepository('AppBundle:Semester')->findAllSemesters();

        $form = $this->createForm(new GenerateMailingListType($semesters));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            $type = $data['type'];
            $semesterID = $data['semester']->getId();

            switch ($type) {
                case 'Assistent':
                    return $this->redirectToRoute('generate_assistant_mail_list', array('id' => $semesterID));
                case 'Team':
                    return $this->redirectToRoute('generate_team_mail_list', array('id' => $semesterID));
                case 'Alle':
                    return $this->redirectToRoute('generate_all_mail_list', array('id' => $semesterID));
                default:
                    throw new InvalidArgumentException('type can only be "Assistent", "Team" or "Alle". Was: '.$type);
            }
        }

        return $this->render('mailing_list/generate_mail_list.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Semester $semester
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAssistantsAction(Semester $semester)
    {
        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findUsersWithAssistantHistoryInSemester($semester);

        return $this->render('mailing_list/mailinglist_show.html.twig', array(
            'users' => $users,
        ));
    }

    /**
     * @param Semester $semester
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showTeamAction(Semester $semester)
    {
        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findUsersWithWorkHistoryInSemester($semester);

        return $this->render('mailing_list/mailinglist_show.html.twig', array(
            'users' => $users,
        ));
    }

    /**
     * @param Semester $semester
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAllAction(Semester $semester)
    {
        $assistantUsers = $this->getDoctrine()->getRepository('AppBundle:User')->findUsersWithAssistantHistoryInSemester($semester);
        $workingUsers = $this->getDoctrine()->getRepository('AppBundle:User')->findUsersWithWorkHistoryInSemester($semester);
        $users = array_merge($assistantUsers, $workingUsers);

        return $this->render('mailing_list/mailinglist_show.html.twig', array(
            'users' => $users,
        ));
    }
}
