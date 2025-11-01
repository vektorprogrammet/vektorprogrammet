<?php

namespace AppBundle\Service;

use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Entity\Application;
use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use AppBundle\Entity\TeamInterest;
use AppBundle\Entity\User;
use AppBundle\Event\ApplicationCreatedEvent;
use AppBundle\Repository\Contract\AdmissionPeriodRepositoryInterface;
use AppBundle\Repository\Contract\ApplicationRepositoryInterface;
use AppBundle\Repository\Contract\TeamRepositoryInterface;
use AppBundle\Service\Contract\AdmissionAdminServiceInterface;
use AppBundle\Service\Contract\InterviewCounterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Service for admission admin operations.
 * Extracts business logic from AdmissionAdminController.
 */
class AdmissionAdminService implements AdmissionAdminServiceInterface
{
    private $admissionPeriodRepository;
    private $applicationRepository;
    private $teamRepository;
    private $interviewCounter;
    private $entityManager;
    private $eventDispatcher;

    /**
     * @param AdmissionPeriodRepositoryInterface $admissionPeriodRepository
     * @param ApplicationRepositoryInterface $applicationRepository
     * @param TeamRepositoryInterface $teamRepository
     * @param InterviewCounterInterface $interviewCounter
     * @param EntityManagerInterface $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        AdmissionPeriodRepositoryInterface $admissionPeriodRepository,
        ApplicationRepositoryInterface $applicationRepository,
        TeamRepositoryInterface $teamRepository,
        InterviewCounterInterface $interviewCounter,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->admissionPeriodRepository = $admissionPeriodRepository;
        $this->applicationRepository = $applicationRepository;
        $this->teamRepository = $teamRepository;
        $this->interviewCounter = $interviewCounter;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getNewApplicationsData(AdmissionPeriod $admissionPeriod): array
    {
        return $this->applicationRepository->findNewApplicationsByAdmissionPeriod($admissionPeriod);
    }

    /**
     * {@inheritdoc}
     */
    public function getAssignedApplicationsData(AdmissionPeriod $admissionPeriod, User $currentUser): array
    {
        $applications = $this->applicationRepository->findAssignedApplicants($admissionPeriod);
        $interviewDistributions = $this->interviewCounter->createInterviewDistributions($applications, $admissionPeriod);
        $cancelledApplications = $this->applicationRepository->findCancelledApplicants($admissionPeriod);
        $applicationsAssignedToUser = $this->applicationRepository->findAssignedByUserAndAdmissionPeriod($currentUser, $admissionPeriod);

        return [
            'applications' => $applications,
            'interviewDistributions' => $interviewDistributions,
            'cancelledApplications' => $cancelledApplications,
            'yourApplications' => $applicationsAssignedToUser,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getInterviewedApplicationsData(AdmissionPeriod $admissionPeriod): array
    {
        $applications = $this->applicationRepository->findInterviewedApplicants($admissionPeriod);

        return [
            'applications' => $applications,
            'yes' => $this->interviewCounter->count($applications, \AppBundle\Service\InterviewCounter::YES),
            'no' => $this->interviewCounter->count($applications, \AppBundle\Service\InterviewCounter::NO),
            'maybe' => $this->interviewCounter->count($applications, \AppBundle\Service\InterviewCounter::MAYBE),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getExistingApplicationsData(AdmissionPeriod $admissionPeriod): array
    {
        return $this->applicationRepository->findExistingApplicants($admissionPeriod);
    }

    /**
     * {@inheritdoc}
     */
    public function bulkDeleteApplications(array $applicationIds): void
    {
        foreach ($applicationIds as $id) {
            $application = $this->entityManager->getRepository(Application::class)->find($id);

            if ($application !== null) {
                $this->entityManager->remove($application);
            }
        }

        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createApplication(Application $application, AdmissionPeriod $admissionPeriod, ?User $existingUser = null): Application
    {
        if ($existingUser !== null) {
            $application->setUser($existingUser);
        }

        $application->setAdmissionPeriod($admissionPeriod);
        $this->entityManager->persist($application);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(ApplicationCreatedEvent::NAME, new ApplicationCreatedEvent($application));

        return $application;
    }

    /**
     * {@inheritdoc}
     */
    public function getTeamInterestData(AdmissionPeriod $admissionPeriod, Semester $semester, Department $department): array
    {
        $applicationsWithTeamInterest = $this->applicationRepository->findApplicationByTeamInterestAndAdmissionPeriod($admissionPeriod);
        $teams = $this->teamRepository->findByTeamInterestAndAdmissionPeriod($admissionPeriod);
        $possibleApplicants = $this->entityManager->getRepository(TeamInterest::class)
            ->findBy(array('semester' => $semester, 'department' => $department));

        return [
            'applicationsWithTeamInterest' => $applicationsWithTeamInterest,
            'possibleApplicants' => $possibleApplicants,
            'teams' => $teams,
        ];
    }
}

