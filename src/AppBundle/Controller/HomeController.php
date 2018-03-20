<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use AppBundle\Entity\User;
use AppBundle\Entity\WorkHistory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    public function showAction()
    {
        $assistantsCount = count($this->getDoctrine()->getRepository('AppBundle:User')->findAssistants());
        $teamMembersCount = count($this->getDoctrine()->getRepository('AppBundle:User')->findTeamMembers());
        $articles = $this->getDoctrine()->getRepository('AppBundle:Article')->findStickyAndLatestArticles();

        $departments = $this->getDoctrine()->getRepository('AppBundle:Department')->findAll();
        $closestDepartment = $this->get('app.geolocation')->findNearestDepartment($departments);

        return $this->render('home/index.html.twig', [
            'assistantCount' => $assistantsCount + 100, // + Estimated number of assistants not registered in website
            'teamMemberCount' => $teamMembersCount + 20, // + Estimated number of team members not registered in website
            'closestDepartment' => $closestDepartment,
            'news' => $articles
        ]);
    }

    public function activeAdmissionAction()
    {
        $activeSemesters = $this->getDoctrine()->getRepository('AppBundle:Semester')->findSemestersWithActiveAdmission();
        $activeDepartments = $this->getDepartmentsFromSemesters($activeSemesters);

        return $this->render('home/dep_with_active_admission.html.twig', array('departments' => $activeDepartments));
    }

    /**
     * @param Semester[] $semesters
     *
     * @return Department[]
     */
    private function getDepartmentsFromSemesters($semesters)
    {
        $departments = [];
        foreach ($semesters as $semester) {
            $department = $semester->getDepartment();
            if (!in_array($department, $departments)) {
                $departments[] = $department;
            }
        }

        return $departments;
    }
}
