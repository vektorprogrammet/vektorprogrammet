<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Semester;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\Type\GenerateMailingListType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class MailingListController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request)
    {
        $form = $this->createForm(new GenerateMailingListType());
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
                    throw new BadRequestHttpException('type can only be "Assistent", "Team" or "Alle". Was: '.$type);
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
        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findUsersWithTeamMembershipInSemester($semester);

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
        $teamUsers = $this->getDoctrine()->getRepository('AppBundle:User')->findUsersWithTeamMembershipInSemester($semester);
        $users = array_unique(array_merge($assistantUsers, $teamUsers));

        return $this->render('mailing_list/mailinglist_show.html.twig', array(
            'users' => $users,
        ));
    }
}
