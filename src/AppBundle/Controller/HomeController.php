<?php

namespace AppBundle\Controller;

use AppBundle\Service\GeoLocation;

class HomeController extends BaseController
{
    public function showAction()
    {
        $geoLocation = $this->get(GeoLocation::class);
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
}
