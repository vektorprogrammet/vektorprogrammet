<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AssistantHistory;
use AppBundle\Entity\Semester;
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

            return $this->render('mailing_list/mailinglist_show.html.twig', array(
                'users' => $this->getUsersByType($data),
            ));
        }

        return $this->render('mailing_list/generate_mail_list.html.twig', array(
            'form' => $form->createView(),
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
    private function getUsersByType($data)
    {
        switch ($data['type']) {
            case 'Assistent':
                return $this->getUsersFromAssistantHistories();
            case 'Team':
                return $this->getUsersFromWorkHistories($data['semester']);
            case 'Begge':
            default:
                $a_users = $this->getUsersFromAssistantHistories();
                $w_users = $this->getUsersFromWorkHistories($data['semester']);

                return array_merge($a_users, $w_users);
        }
    }
}
