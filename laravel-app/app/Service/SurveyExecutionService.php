<?php

namespace App\Service;

use App\Models\AssistantHistory;
use App\Models\Survey;
use App\Models\SurveyLinkClick;
use App\Models\SurveyNotification;
use App\Models\SurveyTaken;
use App\Models\User;
use App\Repository\Contract\AssistantHistoryRepositoryInterface;
use App\Repository\Contract\SemesterRepositoryInterface;
use App\Service\Contract\AccessControlServiceInterface;
use App\Service\Contract\SurveyExecutionServiceInterface;
use App\Service\Contract\SurveyManagerInterface;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Service for handling survey execution and processing operations.
 */
class SurveyExecutionService implements SurveyExecutionServiceInterface
{
    private $surveyManager;
    private $entityManager;
    private $accessControlService;
    private $assistantHistoryRepository;

    /**
     * @param SurveyManagerInterface $surveyManager
     * @param EntityManagerInterface $entityManager
     * @param AccessControlServiceInterface $accessControlService
     * @param AssistantHistoryRepositoryInterface $assistantHistoryRepository
     */
    public function __construct(
        SurveyManagerInterface $surveyManager,
        EntityManagerInterface $entityManager,
        AccessControlServiceInterface $accessControlService,
        AssistantHistoryRepositoryInterface $assistantHistoryRepository
    ) {
        $this->surveyManager = $surveyManager;
        $this->entityManager = $entityManager;
        $this->accessControlService = $accessControlService;
        $this->assistantHistoryRepository = $assistantHistoryRepository;
    }

    /**
     * Execute survey for a user.
     *
     * @param Survey $survey
     * @param User|null $user
     * @param string|null $identifier
     * @return array Contains 'surveyTaken', 'formData', and other execution context
     */
    public function executeSurvey(Survey $survey, User $user = null, string $identifier = null): array
    {
        $result = [
            'surveyTaken' => null,
            'userIdentified' => false,
            'school' => null,
        ];

        // Handle notification-based access
        if ($identifier !== null) {
            $notification = $this->entityManager->getRepository(SurveyNotification::class)
                ->findByUserIdentifier($identifier);

            if ($notification === null) {
                return $result;
            }

            $sameSurvey = $notification->getSurveyNotificationCollection()->getSurvey() == $survey;
            if (!$sameSurvey) {
                return $result;
            }

            // Track link click
            $surveyLinkClick = new SurveyLinkClick();
            $surveyLinkClick->setNotification($notification);
            $this->entityManager->persist($surveyLinkClick);
            $this->entityManager->flush();

            $user = $notification->getUser();
            $result['user'] = $user;
        } else {
            $result['user'] = $user;
        }

        // Initialize survey taken
        if ($user !== null) {
            $result['surveyTaken'] = $this->surveyManager->initializeUserSurveyTaken($survey, $user);
            $result['userIdentified'] = true;

            // Handle assistant surveys - set school from assistant history
            if ($survey->getTargetAudience() === Survey::$ASSISTANT_SURVEY) {
                $assistantHistory = $this->assistantHistoryRepository->findMostRecentByUser($user);
                if (!empty($assistantHistory)) {
                    $assistantHistory = $assistantHistory[0];
                    $school = $assistantHistory->getSchool();
                    $result['surveyTaken']->setSchool($school);
                    $result['school'] = $school;
                }
            }
        } else {
            $result['surveyTaken'] = $this->surveyManager->initializeSurveyTaken($survey);
        }

        return $result;
    }

    /**
     * Process survey submission.
     *
     * @param Survey $survey
     * @param SurveyTaken $surveyTaken
     * @param User|null $user
     * @return void
     */
    public function processSubmission(Survey $survey, SurveyTaken $surveyTaken, User $user = null): void
    {
        $surveyTaken->removeNullAnswers();

        // For user-identified surveys, remove old submissions
        if ($user !== null) {
            $allTakenSurveys = $this->entityManager
                ->getRepository(SurveyTaken::class)
                ->findAllBySurveyAndUser($survey, $user);

            foreach ($allTakenSurveys as $oldTakenSurvey) {
                $this->entityManager->remove($oldTakenSurvey);
            }

            $user->setLastPopUpTime(new DateTime());
            $this->entityManager->persist($user);
        }

        $this->entityManager->persist($surveyTaken);
        $this->entityManager->flush();
    }

    /**
     * Calculate survey statistics.
     *
     * @param Survey $survey
     * @return array Statistics data
     */
    public function calculateStatistics(Survey $survey): array
    {
        $totalAnswered = count($this->entityManager->getRepository(SurveyTaken::class)->findAllTakenBySurvey($survey));
        return [
            'totalAnswered' => $totalAnswered,
        ];
    }

    /**
     * Check if user has access to survey.
     *
     * @param User $user
     * @param Survey $survey
     * @return bool
     */
    public function checkAccess(User $user, Survey $survey): bool
    {
        $isSurveyAdmin = $this->accessControlService->checkAccess("survey_admin");
        $isSameDepartment = $survey->getDepartment() === $user->getDepartment();

        // Confidential surveys require admin access
        if ($survey->isConfidential() && !$isSurveyAdmin) {
            return false;
        }

        // User must be in same department or be survey admin
        return $isSameDepartment || $isSurveyAdmin;
    }

    /**
     * Prepare survey results for display.
     *
     * @param Survey $survey
     * @return array Results data
     */
    public function prepareResults(Survey $survey): array
    {
        if ($survey->getTargetAudience() === Survey::$SCHOOL_SURVEY) {
            $textAnswers = $this->surveyManager->getTextAnswerWithSchoolResults($survey);
        } else {
            $textAnswers = $this->surveyManager->getTextAnswerWithTeamResults($survey);
        }

        return [
            'textAnswers' => $textAnswers,
            'surveyTargetAudience' => $survey->getTargetAudience(),
        ];
    }
}

