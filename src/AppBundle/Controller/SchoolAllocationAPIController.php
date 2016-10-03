<?php

namespace AppBundle\Controller;

use AppBundle\SchoolAllocation\Assistant;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class SchoolAllocationAPIController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

    public function getAssistantsAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $departmentId = $user->getFieldOfStudy()->getDepartment()->getId();

        $currentSemester = $this->getDoctrine()->getRepository('AppBundle:Semester')->findCurrentSemesterByDepartment($departmentId);
        $applications = $this->getDoctrine()->getRepository('AppBundle:Application')->findAllAllocatableApplicationsBySemester($currentSemester);

        $assistants = $this->getAssistantAvailableDays($applications);

        return new JsonResponse(json_encode($assistants));
    }

    private function getAssistantAvailableDays($applications)
    {
        $assistants = array();
        foreach ($applications as $application) {
            $doublePosition = $application->getDoublePosition();
            $preferredGroup = null;
            switch ($application->getPreferredGroup()) {
                case 'Bolk 1': $preferredGroup = 1; break;
                case 'Bolk 2': $preferredGroup = 2; break;
            }
            if ($doublePosition) {
                $preferredGroup = null;
            }

            $availability = array();
            $availabilityBooleans = ['Ikke', 'Bra']; /* False, True */
            $availability['Monday'] = array_search($application->getMonday(), $availabilityBooleans);
            $availability['Tuesday'] = array_search($application->getTuesday(), $availabilityBooleans);
            $availability['Wednesday'] = array_search($application->getWednesday(), $availabilityBooleans);
            $availability['Thursday'] = array_search($application->getThursday(), $availabilityBooleans);
            $availability['Friday'] = array_search($application->getFriday(), $availabilityBooleans);

            $assistant = new Assistant();
            $assistant->setName($application->getUser()->getFirstName().' '.$application->getUser()->getLastName());
            $assistant->setDoublePosition($doublePosition);
            $assistant->setPreferredGroup($preferredGroup);
            $assistant->setAvailability($availability);
            $assistants[] = $assistant;
        }

        return $assistants;
    }
}
