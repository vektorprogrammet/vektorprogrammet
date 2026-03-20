<?php

namespace App\Scheduling\Controller;

use App\Support\Controller\BaseController;
use App\Scheduling\Domain\Rules\Assistant;
use App\Scheduling\Domain\Rules\School;
use App\Entity\Application;
use App\Entity\Repository\AdmissionPeriodRepository;
use App\Entity\Repository\ApplicationRepository;
use App\Entity\Repository\DepartmentRepository;
use App\Scheduling\Infrastructure\Repository\SchoolCapacityRepository;
use App\Shared\Repository\SemesterRepository;
use App\Scheduling\Infrastructure\Entity\SchoolCapacity;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class AssistantSchedulingController extends BaseController
{
    public function __construct(
        private readonly AdmissionPeriodRepository $admissionPeriodRepo,
        private readonly ApplicationRepository $applicationRepo,
        private readonly SchoolCapacityRepository $schoolCapacityRepo,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    #[Route('/kontrollpanel/skole/timeplan/', name: 'school_allocation', methods: ['GET'])]
    public function indexAction()
    {
        return $this->render('assistant_scheduling/index.html.twig');
    }

    /**
     * @return JsonResponse
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    #[Route('/kontrollpanel/api/assistants', name: 'school_allocation_api_assistants', methods: ['GET'])]
    public function getAssistantsAction()
    {
        $user = $this->getUser();

        $currentSemester = $this->getCurrentSemester();

        $currentAdmissionPeriod = $this->admissionPeriodRepo
            ->findOneByDepartmentAndSemester($user->getDepartment(), $currentSemester);
        $applications = $this->applicationRepo->findAllAllocatableApplicationsByAdmissionPeriod($currentAdmissionPeriod);

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
        $assistants = [];
        foreach ($applications as $application) {
            $doublePosition = $application->getDoublePosition();
            $preferredGroup = null;
            switch ($application->getPreferredGroup()) {
                case 'Bolk 1': $preferredGroup = 1;
                    break;
                case 'Bolk 2': $preferredGroup = 2;
                    break;
            }
            if ($doublePosition) {
                $preferredGroup = null;
            }

            $availability = [];
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
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    #[Route('/kontrollpanel/api/schools', name: 'school_allocation_api_schools', methods: ['GET'])]
    public function getSchoolsAction()
    {
        $user = $this->getUser();
        $department = $user->getFieldOfStudy()->getDepartment();
        $currentSemester = $this->getCurrentSemester();
        $allCurrentSchoolCapacities = $this->schoolCapacityRepo->findByDepartmentAndSemester($department, $currentSemester);
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
        // Use schoolCapacities to create School objects for the SA-Algorithm
        $schools = [];
        foreach ($schoolCapacities as $sc) {
            $capacityDays = [];
            $capacityDays['Monday'] = $sc->getMonday();
            $capacityDays['Tuesday'] = $sc->getTuesday();
            $capacityDays['Wednesday'] = $sc->getWednesday();
            $capacityDays['Thursday'] = $sc->getThursday();
            $capacityDays['Friday'] = $sc->getFriday();

            $capacity = [];
            $capacity[1] = $capacityDays;
            $capacity[2] = $capacityDays;

            $school = new School($capacity, $sc->getSchool()->getName(), $sc->getId());
            $schools[] = $school;
        }

        return $schools;
    }
}
