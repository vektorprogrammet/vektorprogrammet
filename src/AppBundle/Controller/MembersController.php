<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MembersController extends Controller
{
    public function showAction()
    {

        // Get manager
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $days = array('Mandag', 'Tirsdag', 'Onsdag', 'Torsdag', 'Fredag', 'Uten dag');
        $department = $user->getFieldOfStudy()->getDepartment();
        $currentSemester = $em->getRepository('AppBundle:Semester')->findCurrentSemesterByDepartment($department);

        $schools = $em->getRepository('AppBundle:School')->findSchoolsByDepartment($department);

        $groups = array();
        foreach (array('Bolk 1', 'Bolk 2') as $group) {
            $groups[$group] = array();
            foreach ($schools as $school) {
                $groups[$group][$school->getName()] = array();

                foreach ($days as $d) {
                    $groups[$group][$school->getName()][$d] = array();
                }

                $assistantHistories = $em->getRepository('AppBundle:AssistantHistory')->findActiveAssistantHistoriesBySchool($school);
                foreach ($assistantHistories as $ah) {
                    if ($ah->getBolk() === $group || $ah->getBolk() === 'Bolk 1, Bolk 2') {
                        if ($ah->getDay() !== '') {
                            array_push($groups[$group][$school->getName()][$ah->getDay()], $ah->getUser());
                        } else {
                            array_push($groups[$group][$school->getName()]['Uten dag'], $ah->getUser());
                        }
                    }
                }
                foreach ($days as $d) {
                    if (count($groups[$group][$school->getName()][$d]) == 0) {
                        unset($groups[$group][$school->getName()][$d]);
                    }
                }
                if (count($groups[$group][$school->getName()]) == 0) {
                    unset($groups[$group][$school->getName()]);
                }
            }
        }

        // Return the view to be rendered
        return $this->render('members/members.html.twig', array(
            'groups' => $groups,
            'department' => $department,
            'semester' => $currentSemester,
        ));
    }
}
