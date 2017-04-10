<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AssistantHistory;
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
            $semester_id = $data['semester']->getId();

            switch ($type) {
                case 'Assistent':
                    return $this->redirectToRoute('generate_assistant_mail_list', array('semester_id' => $semester_id));
                case 'Team':
                    return $this->redirectToRoute('generate_team_mail_list', array('semester_id' => $semester_id));
                case 'Begge':
                    return $this->redirectToRoute('generate_all_mail_list', array('semester_id' => $semester_id));
                default:
                    throw new InvalidArgumentException('type can only be "Assistent", "Team" or "Begge". Was: '.$type);
            }
        }

        return $this->render('mailing_list/generate_mail_list.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @param int $semester_id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAssistantsAction(int $semester_id)
    {
        $semester = $this->getDoctrine()->getRepository('AppBundle:Semester')->find($semester_id);
        $type = 'Assistent';

        return $this->render('mailing_list/mailinglist_show.html.twig', array(
            'users' => $this->getUsersByTypeSemester($type, $semester),
        ));
    }

    /**
     * @param int $semester_id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showTeamAction(int $semester_id)
    {
        $semester = $this->getDoctrine()->getRepository('AppBundle:Semester')->find($semester_id);
        $type = 'Team';

        return $this->render('mailing_list/mailinglist_show.html.twig', array(
            'users' => $this->getUsersByTypeSemester($type, $semester),
        ));
    }

    /**
     * @param int $semester_id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAllAction(int $semester_id)
    {
        $semester = $this->getDoctrine()->getRepository('AppBundle:Semester')->find($semester_id);
        $type = 'Begge';

        return $this->render('mailing_list/mailinglist_show.html.twig', array(
            'users' => $this->getUsersByTypeSemester($type, $semester),
        ));
    }

    /**
     * @param Semester $semester
     *
     * @return array
     */
    private function getUsersFromWorkHistories(Semester $semester)
    {
        $work_histories = $this->getDoctrine()->getRepository('AppBundle:WorkHistory')->findActiveWorkHistories();
        $users = array();
        foreach ($work_histories as $wh) {
            if ($wh->isActiveInSemester($semester)) {
                $users[] = $wh->getUser();
            }
        }

        return $users;
    }

    /**
     * @return array
     */
    private function getUsersFromAssistantHistories()
    {
        $assistant_histories = $this->getDoctrine()->getRepository('AppBundle:AssistantHistory')->findAllActiveAssistantHistories();
        $getUsersFromAssistants = function (AssistantHistory $assistant) {
            return $assistant->getUser();
        };
        $users = array_map($getUsersFromAssistants, $assistant_histories);

        return $users;
    }

    /**
     * @param $data
     *
     * @return array
     */
    private function getUsersByTypeSemester($type, $semester)
    {
        switch ($type) {
            case 'Assistent':
                return $this->getUsersFromAssistantHistories();
            case 'Team':
                return $this->getUsersFromWorkHistories($semester);
            case 'Begge':
                $a_users = $this->getUsersFromAssistantHistories();
                $w_users = $this->getUsersFromWorkHistories($semester);

                return array_merge($a_users, $w_users);
            default:
                throw new InvalidArgumentException('type can only be "Assistent", "Team" or "Begge". Was: '.$type);
        }
    }
}
