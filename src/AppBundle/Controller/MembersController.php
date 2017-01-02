<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MembersController extends Controller
{
    public function showAction()
    {
        $user = $this->getUser();

        $department = $user->getFieldOfStudy()->getDepartment();
        $currentSemester = $this->getDoctrine()->getRepository('AppBundle:Semester')->findCurrentSemesterByDepartment($department);

        $groups = $this->get('app.assistants.data')->getCurrentAssistantDataByDepartment($department, $currentSemester);

        // Return the view to be rendered
        return $this->render('members/members.html.twig', array(
            'groups' => $groups,
            'department' => $department,
            'semester' => $currentSemester,
        ));
    }
}
