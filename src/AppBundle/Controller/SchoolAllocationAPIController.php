<?php

namespace AppBundle\Controller;

use AppBundle\SchoolAllocation\Assistant;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\SchoolAllocation\School;
use AppBundle\SchoolAllocation\Allocation;

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

    /**
     * @param Application[] $applications
     *
     * @return Assistant[]
     */
    private function generateAssistantsFromApplications($applications)
    {
        //Use applications to create Assistant objects for the allocation algorithm
        $assistants = array();
        foreach ($applications as $application) {
            $doublePosition = $application->getDoublePosition();
            $preferredGroup = null;
            if ($application->getPreferredGroup() == 'Bolk 1') {
                $preferredGroup = 1;
            } elseif ($application->getPreferredGroup() == 'Bolk 2') {
                $preferredGroup = 2;
            }
            if ($doublePosition) {
                $preferredGroup = null;
            }

            $availability = array();
            $availabilityPoints = ['Ikke', 'Bra'];
            $availability['Monday'] = array_search($application->getMonday(), $availabilityPoints);
            $availability['Tuesday'] = array_search($application->getTuesday(), $availabilityPoints);
            $availability['Wednesday'] = array_search($application->getWednesday(), $availabilityPoints);
            $availability['Thursday'] = array_search($application->getThursday(), $availabilityPoints);
            $availability['Friday'] = array_search($application->getFriday(), $availabilityPoints);

            $assistant = new Assistant();
            $assistant->setName($application->getUser()->getFirstName().' '.$application->getUser()->getLastName());
            $assistant->setDoublePosition($doublePosition);
            $assistant->setPreferredGroup($preferredGroup);
            $assistant->setAvailability($availability);
            $assistants[] = $assistant;
        }

        return $assistants;
    }

    private function generateSchoolsFromSchoolCapacities($schoolCapacities)
    {
        //Use schoolCapacities to create School objects for the SA-Algorithm
        $schools = array();
        foreach ($schoolCapacities as $sc) {
            if ($sc->getMonday() == 0 && $sc->getTuesday() == 0 && $sc->getWednesday() == 0 && $sc->getThursday() == 0 && $sc->getFriday() == 0) {
                continue;
            }
            $capacityDays = array();
            $capacityDays['Monday'] = $sc->getMonday();
            $capacityDays['Tuesday'] = $sc->getTuesday();
            $capacityDays['Wednesday'] = $sc->getWednesday();
            $capacityDays['Thursday'] = $sc->getThursday();
            $capacityDays['Friday'] = $sc->getFriday();

            $capacity = array();
            $capacity[1] = $capacityDays;
            $capacity[2] = $capacityDays;

            $school = new School($capacity, $sc->getSchool()->getName());
            $schools[] = $school;
        }

        return $schools;
    }

    // FIX: Change the name of this function to reflect what it does
    public function generateSchoolsFromSchoolCapacitiesAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $departmentId = $user->getFieldOfStudy()->getDepartment()->getId();
        $currentSemester = $this->getDoctrine()->getRepository('AppBundle:Semester')->findCurrentSemesterByDepartment($departmentId);
        $allCurrentSchoolCapacities = $this->getDoctrine()->getRepository('AppBundle:SchoolCapacity')->findBySemester($currentSemester);
        $schools = $this->generateSchoolsFromSchoolCapacities($allCurrentSchoolCapacities);

        // Array for fast lookup on school availability by days
        $school_availability = array(
            'Mandag' => array(),
            'Tirsdag' => array(),
            'Onsdag' => array(),
            'Torsdag' => array(),
            'Fredag' => array(),
        );
        foreach ($schools as $school) {
            foreach ($school->getCapacity()[1] as $day => $is_avail) {
                if ($is_avail) {
                    $norwegian_day = '';
                    switch ($day) {
                        case 'Monday':    $norwegian_day = 'Mandag'; break;
                        case 'Tuesday':   $norwegian_day = 'Tirsdag'; break;
                        case 'Wednesday': $norwegian_day = 'Onsdag'; break;
                        case 'Thursday':  $norwegian_day = 'Torsdag'; break;
                        case 'Friday':    $norwegian_day = 'Fredag'; break;
                    }
                    $school_availability[$norwegian_day][] = $school->getName();
                }
            }
        }

        return new JsonResponse(json_encode($school_availability));
    }

    public function generateAllSchoolsAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $departmentId = $user->getFieldOfStudy()->getDepartment()->getId();
        $currentSemester = $this->getDoctrine()->getRepository('AppBundle:Semester')->findCurrentSemesterByDepartment($departmentId);
        $allCurrentSchoolCapacities = $this->getDoctrine()->getRepository('AppBundle:SchoolCapacity')->findBySemester($currentSemester);
        $schools = $this->generateSchoolsFromSchoolCapacities($allCurrentSchoolCapacities);

        return new JsonResponse(json_encode($schools));
    }

    public function getAllocatedAssistantsAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $departmentId = $user->getFieldOfStudy()->getDepartment()->getId();

        $currentSemester = $this->getDoctrine()->getRepository('AppBundle:Semester')->findCurrentSemesterByDepartment($departmentId);

        $allCurrentSchoolCapacities = $this->getDoctrine()->getRepository('AppBundle:SchoolCapacity')->findBySemester($currentSemester);
        $applications = $this->getDoctrine()->getRepository('AppBundle:Application')->findAllAllocatableApplicationsBySemester($currentSemester);
        $assistants = $this->generateAssistantsFromApplications($applications);
        $schools = $this->generateSchoolsFromSchoolCapacities($allCurrentSchoolCapacities);
        $allocation = new Allocation($schools, $assistants);
        $result = $allocation->step();

        //Total number of allocations evaluated during optimization

        /*return $this->render('school_admin/school_allocate_result.html.twig', array(
            'assistants' => $result->getAssistants(),
        ));*/

        $allocated_assistants = $result->getAssistants();
        foreach ($allocated_assistants as $assistant) {
            $availability = $assistant->getAvailability();
            foreach ($availability as $day => $avail) {
                switch ($day) {
                    case 'Monday': $availability['Mandag'] = $avail; break;
                    case 'Tuesday': $availability['Tirsdag'] = $avail; break;
                    case 'Wednesday': $availability['Onsdag'] = $avail; break;
                    case 'Thursday': $availability['Torsdag'] = $avail; break;
                    case 'Friday': $availability['Fredag'] = $avail; break;
                }
                unset($availability[$day]);
            }
            $assistant->setAvailability($availability);
        }

        return new JsonResponse(json_encode($allocated_assistants));
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
