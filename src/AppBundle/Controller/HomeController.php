<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use AppBundle\Entity\User;
use AppBundle\Entity\TeamMembership;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    public function showAction()
    {
        $geoLocation = $this->get('app.geolocation');
        $assistantsCount = count($this->getDoctrine()->getRepository('AppBundle:User')->findAssistants());
        $teamMembersCount = count($this->getDoctrine()->getRepository('AppBundle:User')->findTeamMembers());
        $articles = $this->getDoctrine()->getRepository('AppBundle:Article')->findStickyAndLatestArticles();

        $departments = $this->getDoctrine()->getRepository('AppBundle:Department')->findAll();
        $departmentsWithActiveAdmission = $this->getDoctrine()->getRepository('AppBundle:Department')->findAllWithActiveAdmission();
        $departmentsWithActiveAdmission = $geoLocation->sortDepartmentsByDistanceFromClient($departmentsWithActiveAdmission);
        $closestDepartment = $geoLocation->findNearestDepartment($departments);
        $ipWasLocated = $geoLocation->findCoordinatesOfCurrentRequest();

        $femaleAssistantCount = $this->getDoctrine()->getRepository('AppBundle:AssistantHistory')->numFemale();
        $maleAssistantCount = $this->getDoctrine()->getRepository('AppBundle:AssistantHistory')->numMale();

        return $this->render('home/index.html.twig', [
            'assistantCount' => $assistantsCount + 600, // + Estimated number of assistants not registered in website
            'teamMemberCount' => $teamMembersCount + 160, // + Estimated number of team members not registered in website
            'femaleAssistantCount' => $femaleAssistantCount,
            'maleAssistantCount' => $maleAssistantCount,
            'ipWasLocated' => $ipWasLocated,
            'departmentsWithActiveAdmission' => $departmentsWithActiveAdmission,
            'closestDepartment' => $closestDepartment,
            'news' => $articles,
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
