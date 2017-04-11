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
                    return $this->redirectToRoute('generate_assistant_mail_list', array('semesterID' => $semesterID));
                case 'Team':
                    return $this->redirectToRoute('generate_team_mail_list', array('semesterID' => $semesterID));
                case 'Alle':
                    return $this->redirectToRoute('generate_all_mail_list', array('semesterID' => $semesterID));
                default:
                    throw new InvalidArgumentException('type can only be "Assistent", "Team" or "Alle". Was: '.$type);
            }
        }

        return $this->render('mailing_list/generate_mail_list.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @param int $semesterID
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAssistantsAction(int $semesterID)
    {
        $semester = $this->getDoctrine()->getRepository('AppBundle:Semester')->find($semesterID);
        $type = 'Assistent';

        return $this->render('mailing_list/mailinglist_show.html.twig', array(
            'users' => $this->getUsersByTypeSemester($type, $semester),
        ));
    }

    /**
     * @param int $semesterID
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showTeamAction(int $semesterID)
    {
        $semester = $this->getDoctrine()->getRepository('AppBundle:Semester')->find($semesterID);
        $type = 'Team';

        return $this->render('mailing_list/mailinglist_show.html.twig', array(
            'users' => $this->getUsersByTypeSemester($type, $semester),
        ));
    }

    /**
     * @param int $semesterID
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAllAction(int $semesterID)
    {
        $semester = $this->getDoctrine()->getRepository('AppBundle:Semester')->find($semesterID);
        $type = 'Alle';

        return $this->render('mailing_list/mailinglist_show.html.twig', array(
            'users' => $this->getUsersByTypeSemester($type, $semester),
        ));
    }

    /**
     * @param string   $type
     * @param Semester $semester
     *
     * @return array
     */
    private function getUsersByTypeSemester(string $type, Semester $semester)
    {
        switch ($type) {
            case 'Assistent':
                return $this->getDoctrine()->getRepository('AppBundle:User')->findUsersWithAssistantHistoryInSemester($semester);
            case 'Team':
                return $this->getDoctrine()->getRepository('AppBundle:User')->findUsersWithWorkHistoryInSemester($semester);
            case 'Alle':
                $assistantUsers = $this->getDoctrine()->getRepository('AppBundle:User')->findUsersWithAssistantHistoryInSemester($semester);
                $workingUsers = $this->getDoctrine()->getRepository('AppBundle:User')->findUsersWithWorkHistoryInSemester($semester);

                return array_merge($assistantUsers, $workingUsers);
            default:
                throw new InvalidArgumentException('type can only be "Assistent", "Team" or "Alle". Was: '.$type);
        }
    }
}
