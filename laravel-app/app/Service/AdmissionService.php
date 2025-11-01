<?php

namespace App\Service;

use App\Models\AdmissionPeriod;
use App\Models\Application;
use App\Models\Department;
use App\Models\Team;
use App\Event\ApplicationCreatedEvent;
use App\Repository\Contract\AdmissionPeriodRepositoryInterface;
use App\Repository\Contract\DepartmentRepositoryInterface;
use App\Repository\Contract\TeamRepositoryInterface;
use App\Service\Contract\AdmissionServiceInterface;
use App\Service\Contract\ApplicationAdmissionInterface;
use App\Service\Contract\FilterServiceInterface;
use App\Service\Contract\GeoLocationInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Service for handling admission page preparation and application submission.
 */
class AdmissionService implements AdmissionServiceInterface
{
    private $admissionManager;
    private $entityManager;
    private $departmentRepository;
    private $geoLocation;
    private $filterService;
    private $teamRepository;
    private $admissionPeriodRepository;
    private $eventDispatcher;

    /**
     * @param ApplicationAdmissionInterface $admissionManager
     * @param EntityManagerInterface $entityManager
     * @param DepartmentRepositoryInterface $departmentRepository
     * @param GeoLocationInterface $geoLocation
     * @param FilterServiceInterface $filterService
     * @param TeamRepositoryInterface $teamRepository
     * @param AdmissionPeriodRepositoryInterface $admissionPeriodRepository
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        ApplicationAdmissionInterface $admissionManager,
        EntityManagerInterface $entityManager,
        DepartmentRepositoryInterface $departmentRepository,
        GeoLocationInterface $geoLocation,
        FilterServiceInterface $filterService,
        TeamRepositoryInterface $teamRepository,
        AdmissionPeriodRepositoryInterface $admissionPeriodRepository,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->admissionManager = $admissionManager;
        $this->entityManager = $entityManager;
        $this->departmentRepository = $departmentRepository;
        $this->geoLocation = $geoLocation;
        $this->filterService = $filterService;
        $this->teamRepository = $teamRepository;
        $this->admissionPeriodRepository = $admissionPeriodRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Prepare admission page data.
     *
     * @param Department|null $specificDepartment
     * @return array Contains 'departments', 'departmentsWithActiveAdmission', 'teams', etc.
     */
    public function prepareAdmissionPageData(Department $specificDepartment = null): array
    {
        $departments = $this->departmentRepository->findActive();
        $departments = $this->geoLocation->sortDepartmentsByDistanceFromClient($departments);
        $departmentsWithActiveAdmission = $this->filterService->filterDepartmentsByActiveAdmission($departments, true);

        // If no specific department provided, use the first one
        if ($specificDepartment === null) {
            $specificDepartment = !empty($departments) ? $departments[0] : null;
        }

        $teams = [];
        if ($specificDepartment !== null) {
            $teams = $this->teamRepository->findByOpenApplicationAndDepartment($specificDepartment);
        }

        return [
            'departments' => $departments,
            'departmentsWithActiveAdmission' => $departmentsWithActiveAdmission,
            'specificDepartment' => $specificDepartment,
            'teams' => $teams,
        ];
    }

    /**
     * Submit an application.
     *
     * @param Application $application
     * @param Department $department
     * @return array Contains 'application', 'admissionPeriod', 'redirectRoute' or 'error'
     */
    public function submitApplication(Application $application, Department $department): array
    {
        $this->admissionManager->setCorrectUser($application);

        // Check if user has been assistant before
        if ($application->getUser()->hasBeenAssistant()) {
            return [
                'application' => $application,
                'redirectRoute' => 'admission_existing_user',
                'hasBeenAssistant' => true,
            ];
        }

        $admissionPeriod = $this->admissionPeriodRepository->findOneWithActiveAdmissionByDepartment($department);

        // If no active admission period is found
        if (!$admissionPeriod) {
            return [
                'application' => $application,
                'error' => $department . ' sitt opptak er dessverre stengt.',
            ];
        }

        $application->setAdmissionPeriod($admissionPeriod);
        $this->entityManager->persist($application);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(ApplicationCreatedEvent::NAME, new ApplicationCreatedEvent($application));

        return [
            'application' => $application,
            'admissionPeriod' => $admissionPeriod,
            'redirectRoute' => 'application_confirmation',
        ];
    }

    /**
     * Find department by city (case-insensitive).
     *
     * @param string $city
     * @return Department|null
     */
    public function findDepartmentByCity(string $city): ?Department
    {
        // Normalize for SQLite compatibility
        $city = str_replace(array('æ', 'ø','å'), array('Æ','Ø','Å'), $city);
        return $this->departmentRepository->findOneByCityCaseInsensitive($city);
    }

    /**
     * Get teams for department that have open applications.
     *
     * @param Department $department
     * @return array
     */
    public function getTeamsForDepartment(Department $department): array
    {
        return $this->teamRepository->findByOpenApplicationAndDepartment($department);
    }

    /**
     * Validate and get active admission period for department.
     *
     * @param Department $department
     * @return AdmissionPeriod|null
     */
    public function validateAdmissionPeriod(Department $department): ?AdmissionPeriod
    {
        return $this->admissionPeriodRepository->findOneWithActiveAdmissionByDepartment($department);
    }
}

