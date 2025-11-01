<?php

namespace AppBundle\Service;

use AppBundle\Entity\Application;
use AppBundle\Entity\Interview;
use AppBundle\Entity\InterviewSchema;
use AppBundle\Entity\User;
use AppBundle\Event\InterviewConductedEvent;
use AppBundle\Event\InterviewEvent;
use AppBundle\Repository\Contract\ApplicationRepositoryInterface;
use AppBundle\Role\Roles;
use AppBundle\Service\Contract\InterviewManagerInterface;
use AppBundle\Service\Contract\InterviewSchedulingServiceInterface;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Service for handling interview scheduling and assignment operations.
 */
class InterviewSchedulingService implements InterviewSchedulingServiceInterface
{
    private $interviewManager;
    private $entityManager;
    private $eventDispatcher;
    private $applicationRepository;
    private $authorizationChecker;

    /**
     * @param InterviewManagerInterface $interviewManager
     * @param EntityManagerInterface $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param ApplicationRepositoryInterface $applicationRepository
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        InterviewManagerInterface $interviewManager,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        ApplicationRepositoryInterface $applicationRepository,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->interviewManager = $interviewManager;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->applicationRepository = $applicationRepository;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * Schedule an interview with the provided form data.
     *
     * @param Interview $interview
     * @param array $formData
     * @return void
     */
    public function scheduleInterview(Interview $interview, array $formData): void
    {
        $mapLink = $formData['mapLink'] ?? null;
        
        // Normalize map link
        if ($mapLink && !(strpos($mapLink, 'http') === 0)) {
            $mapLink = 'http://' . $mapLink;
        }

        // Generate response code if not exists
        if (!$interview->getResponseCode()) {
            $interview->generateAndSetResponseCode();
        }

        // Update interview scheduling information
        $interview->setScheduled($formData['datetime']);
        $interview->setRoom($formData['room']);
        $interview->setCampus($formData['campus']);
        $interview->setMapLink($mapLink);
        $interview->resetStatus();

        $this->entityManager->persist($interview);
        $this->entityManager->flush();
    }

    /**
     * Assign an interviewer to an application with a schema.
     *
     * @param User $interviewer
     * @param Application $application
     * @param InterviewSchema $schema
     * @return void
     */
    public function assignInterviewer(User $interviewer, Application $application, InterviewSchema $schema): void
    {
        $this->interviewManager->assignInterviewerToApplication($interviewer, $application);
        $application->getInterview()->setInterviewSchema($schema);
        $application->getInterview()->setUser($application->getUser());
        
        $this->entityManager->persist($application);
        $this->entityManager->flush();
    }

    /**
     * Bulk assign interviews to multiple applications.
     *
     * @param User $interviewer
     * @param array $applications
     * @param InterviewSchema $schema
     * @return void
     */
    public function bulkAssignInterviews(User $interviewer, array $applications, InterviewSchema $schema): void
    {
        foreach ($applications as $application) {
            $this->interviewManager->assignInterviewerToApplication($interviewer, $application);
            $application->getInterview()->setInterviewSchema($schema);
            $this->entityManager->persist($application);
        }

        $this->entityManager->flush();
    }

    /**
     * Validate a map link URL.
     *
     * @param string $link
     * @return bool
     */
    public function validateMapLink(string $link): bool
    {
        if (empty($link)) {
            return false;
        }

        try {
            $headers = get_headers($link);
            if ($headers === false) {
                return false;
            }
            $statusCode = intval(explode(" ", $headers[0])[1]);
        } catch (Exception $e) {
            return false;
        }

        return $statusCode < 400;
    }

    /**
     * Process interview response (accept/cancel/new time request).
     *
     * @param Interview $interview
     * @param string $action One of: 'accept', 'cancel', 'request_new_time'
     * @param array|null $data Additional data for the action
     * @return void
     */
    public function processInterviewResponse(Interview $interview, string $action, array $data = null): void
    {
        switch ($action) {
            case 'accept':
                $interview->acceptInterview();
                break;
            case 'cancel':
                if (isset($data['message'])) {
                    $interview->setCancelMessage($data['message']);
                }
                $interview->cancel();
                $this->interviewManager->sendCancelEmail($interview);
                break;
            case 'request_new_time':
                $interview->requestNewTime();
                $this->interviewManager->sendRescheduleEmail($interview);
                break;
        }

        $this->entityManager->persist($interview);
        $this->entityManager->flush();
    }

    /**
     * Check if user can access interview.
     *
     * @param User $user
     * @param Interview $interview
     * @return bool
     */
    public function canUserAccessInterview(User $user, Interview $interview): bool
    {
        // Use the existing InterviewManager method
        return $this->interviewManager->loggedInUserCanSeeInterview($interview);
    }

    /**
     * Conduct interview and mark as interviewed.
     *
     * @param Interview $interview
     * @param Application $application
     * @return void
     */
    public function conductInterview(Interview $interview, Application $application): void
    {
        $isNewInterview = !$interview->getInterviewed();
        $interview->setCancelled(false);
        $interview->setInterviewed(true);
        $interview->setConducted(new DateTime());

        $this->entityManager->persist($interview);
        $this->entityManager->flush();

        if ($isNewInterview) {
            $this->eventDispatcher->dispatch(InterviewConductedEvent::NAME, new InterviewConductedEvent($application));
        }
    }

    /**
     * Send schedule email for interview.
     *
     * @param Interview $interview
     * @param array $data
     * @return void
     */
    public function sendScheduleEmail(Interview $interview, array $data): void
    {
        $this->eventDispatcher->dispatch(InterviewEvent::SCHEDULE, new InterviewEvent($interview, $data));
    }
}

