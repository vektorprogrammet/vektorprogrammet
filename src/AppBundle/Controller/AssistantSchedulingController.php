<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Application;
use AppBundle\AssistantScheduling\Assistant;
use AppBundle\AssistantScheduling\School;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class AssistantSchedulingController extends Controller
{
    public function indexAction()
    {
        return $this->render('assistant_scheduling/index.html.twig');
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
	 * @return array
	 */
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
			$assistant->setApplication($application);
			$assistant->setScore($application->getInterview()->getScore());
			$assistant->setSuitability($application->getInterview()->getInterviewScore()->getSuitableAssistant());
			$assistant->setPreviousParticipation($application->getPreviousParticipation());
			$assistants[] = $assistant;
		}

		return $assistants;
	}

	public function getSchoolsAction()
	{
		$user = $this->get('security.token_storage')->getToken()->getUser();
		$departmentId = $user->getFieldOfStudy()->getDepartment()->getId();
		$currentSemester = $this->getDoctrine()->getRepository('AppBundle:Semester')->findCurrentSemesterByDepartment($departmentId);
		$allCurrentSchoolCapacities = $this->getDoctrine()->getRepository('AppBundle:SchoolCapacity')->findBySemester($currentSemester);
		$schools = $this->generateSchoolsFromSchoolCapacities($allCurrentSchoolCapacities);

		return new JsonResponse(json_encode($schools));
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

			$school = new School($capacity, $sc->getSchool()->getName(), $sc->getId());
			$schools[] = $school;
		}

		return $schools;
	}

}
