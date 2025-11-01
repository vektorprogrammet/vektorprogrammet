<?php

namespace AppBundle\Controller;

use AppBundle\AssistantScheduling\Assistant;
use AppBundle\AssistantScheduling\School;
use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Entity\Application;
use AppBundle\Entity\SchoolCapacity;
use AppBundle\Entity\Semester;
use AppBundle\Repository\Contract\AdmissionPeriodRepositoryInterface;
use AppBundle\Repository\Contract\ApplicationRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpFoundation\JsonResponse;

class AssistantSchedulingController extends BaseController
{
    private $admissionPeriodRepository;
    private $applicationRepository;
    private $entityManager;

    /**
     * @param AdmissionPeriodRepositoryInterface $admissionPeriodRepository
     * @param ApplicationRepositoryInterface $applicationRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        AdmissionPeriodRepositoryInterface $admissionPeriodRepository,
        ApplicationRepositoryInterface $applicationRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->admissionPeriodRepository = $admissionPeriodRepository;
        $this->applicationRepository = $applicationRepository;
        $this->entityManager = $entityManager;
    }
    public function indexAction()
    {
        return $this->render('assistant_scheduling/index.html.twig');
    }

    /**
     * @return JsonResponse
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getAssistantsAction()
    {
        $user = $this->getUser();

        $currentSemester = $this->getCurrentSemester();

        $currentAdmissionPeriod = $this->admissionPeriodRepository->findOneByDepartmentAndSemester($user->getDepartment(), $currentSemester);
        $applications = $this->applicationRepository->findAllAllocatableApplicationsByAdmissionPeriod($currentAdmissionPeriod);

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
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getSchoolsAction()
    {
        $user = $this->getUser();
        $department = $user->getFieldOfStudy()->getDepartment();
        $currentSemester = $this->getCurrentSemester();
        $allCurrentSchoolCapacities = $this->entityManager->getRepository(SchoolCapacity::class)->findByDepartmentAndSemester($department, $currentSemester);
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
