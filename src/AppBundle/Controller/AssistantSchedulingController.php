<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Application;
use AppBundle\AssistantScheduling\Assistant;
use AppBundle\AssistantScheduling\School;
use AppBundle\Entity\SchoolCapacity;
use Symfony\Component\HttpFoundation\JsonResponse;

class AssistantSchedulingController extends BaseController
{
    public function indexAction()
    {
        return $this->render('assistant_scheduling/index.html.twig');
    }

    /**
     * @return JsonResponse
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getAssistantsAction()
    {
        $user = $this->getUser();

        $currentSemester = $this->getDoctrine()->getRepository('AppBundle:Semester')->findCurrentSemester();
        $currentAdmissionPeriod = $this->getDoctrine()->getRepository('AppBundle:AdmissionPeriod')
            ->findOneByDepartmentAndSemester($user->getDepartment(), $currentSemester);
        $applications = $this->getDoctrine()->getRepository('AppBundle:Application')->findAllAllocatableApplicationsByAdmissionPeriod($currentAdmissionPeriod);

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
            $availability['Monday'] = $application->isMonday();
            $availability['Tuesday'] = $application->isTuesday();
            $availability['Wednesday'] = $application->isWednesday();
            $availability['Thursday'] = $application->isThursday();
            $availability['Friday'] = $application->isFriday();

            $assistant = new Assistant();
            $assistant->setName($application->getUser()->getFullName());
            $assistant->setEmail($application->getUser()->getEmail());
            $assistant->setDoublePosition($doublePosition);
            $assistant->setPreferredGroup($preferredGroup);
            $assistant->setAvailability($availability);
            $assistant->setApplication($application);
            if ($application->getPreviousParticipation()) {
                $assistant->setSuitability('Ja');
                $assistant->setScore(20);
            } else {
                $assistant->setScore($application->getInterview()->getScore());
                $assistant->setSuitability($application->getInterview()->getInterviewScore()->getSuitableAssistant());
            }
            $assistant->setPreviousParticipation($application->getPreviousParticipation());
            $assistants[] = $assistant;
        }

        return $assistants;
    }

    /**
     * @return JsonResponse
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getSchoolsAction()
    {
        $user = $this->getUser();
        $department = $user->getFieldOfStudy()->getDepartment();
        $currentSemester = $this->getDoctrine()->getRepository('AppBundle:Semester')->findCurrentSemester();
        $allCurrentSchoolCapacities = $this->getDoctrine()
            ->getRepository('AppBundle:SchoolCapacity')->findByDepartmentAndSemester($department, $currentSemester);
        $schools = $this->generateSchoolsFromSchoolCapacities($allCurrentSchoolCapacities);

        return new JsonResponse(json_encode($schools));
    }

    /**
     * @param SchoolCapacity[] $schoolCapacities
     *
     * @return array
     */
    private function generateSchoolsFromSchoolCapacities($schoolCapacities)
    {
        //Use schoolCapacities to create School objects for the SA-Algorithm
        $schools = array();
        foreach ($schoolCapacities as $sc) {
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
