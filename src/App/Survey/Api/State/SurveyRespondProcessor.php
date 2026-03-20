<?php

namespace App\Survey\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Operations\Infrastructure\Repository\AssistantHistoryRepository;
use App\Survey\Infrastructure\Repository\SurveyTakenRepository;
use App\Survey\Infrastructure\Entity\Survey;
use App\Survey\Infrastructure\SurveyManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class SurveyRespondProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly EntityManagerInterface $em,
        private readonly SurveyManager $surveyManager,
        private readonly SurveyTakenRepository $surveyTakenRepo,
        private readonly AssistantHistoryRepository $assistantHistoryRepo,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $surveyId = $uriVariables['id'] ?? null;
        $survey = $this->em->getRepository(Survey::class)->find($surveyId);
        if (!$survey) {
            throw new NotFoundHttpException('Survey not found');
        }

        $user = $this->security->getUser();
        $audience = $survey->getTargetAudience();

        if ($audience === Survey::$SCHOOL_SURVEY) {
            $this->handleSchoolSurvey($survey, $data);
        } elseif ($audience === Survey::$ASSISTANT_SURVEY) {
            if ($user === null) {
                throw new UnauthorizedHttpException('Bearer', 'Authentication required');
            }
            $this->handleAssistantSurvey($survey, $user, $data);
        } elseif ($audience === Survey::$TEAM_SURVEY) {
            if ($user === null) {
                throw new UnauthorizedHttpException('Bearer', 'Authentication required');
            }
            $this->handleTeamSurvey($survey, $user, $data);
        }
    }

    private function handleSchoolSurvey(Survey $survey, mixed $data): void
    {
        $surveyTaken = $this->surveyManager->initializeSurveyTaken($survey);
        $this->mapAnswers($surveyTaken, $data->answers);
        $surveyTaken->removeNullAnswers();
        $this->em->persist($surveyTaken);
        $this->em->flush();
    }

    private function handleAssistantSurvey(Survey $survey, mixed $user, mixed $data): void
    {
        $assistantHistories = $this->assistantHistoryRepo->findMostRecentByUser($user);
        if ($assistantHistories === []) {
            throw new BadRequestHttpException('No assistant history found');
        }

        $surveyTaken = $this->surveyManager->initializeUserSurveyTaken($survey, $user);
        $surveyTaken->setSchool($assistantHistories[0]->getSchool());

        $this->deleteOldResponsesAndUpdatePopUpTime($survey, $user);
        $this->mapAnswers($surveyTaken, $data->answers);
        $surveyTaken->removeNullAnswers();
        $this->em->persist($user);
        $this->em->persist($surveyTaken);
        $this->em->flush();
    }

    private function handleTeamSurvey(Survey $survey, mixed $user, mixed $data): void
    {
        $surveyTaken = $this->surveyManager->initializeUserSurveyTaken($survey, $user);

        $this->deleteOldResponsesAndUpdatePopUpTime($survey, $user);
        $this->mapAnswers($surveyTaken, $data->answers);
        $surveyTaken->removeNullAnswers();
        $this->em->persist($user);
        $this->em->persist($surveyTaken);
        $this->em->flush();
    }

    /**
     * Delete any previous SurveyTaken records for this survey+user and update lastPopUpTime.
     * Matches the original controller's showUserMainAction behavior for both
     * TEAM_SURVEY and ASSISTANT_SURVEY audience types.
     */
    private function deleteOldResponsesAndUpdatePopUpTime(Survey $survey, mixed $user): void
    {
        $oldResponses = $this->surveyTakenRepo->findAllBySurveyAndUser($survey, $user);
        foreach ($oldResponses as $oldResponse) {
            $this->em->remove($oldResponse);
        }

        $user->setLastPopUpTime(new \DateTime());
    }

    /**
     * Map answer data from the input onto the SurveyAnswer entities created by SurveyManager.
     *
     * @param \App\Survey\Infrastructure\Entity\SurveyTaken            $surveyTaken
     * @param array<array{questionId: int, answer: string|string[]}> $answers
     */
    private function mapAnswers($surveyTaken, array $answers): void
    {
        // Index input answers by questionId for fast lookup
        $answerMap = [];
        foreach ($answers as $answerData) {
            $answerMap[$answerData['questionId']] = $answerData['answer'];
        }

        foreach ($surveyTaken->getSurveyAnswers() as $surveyAnswer) {
            $questionId = $surveyAnswer->getSurveyQuestion()->getId();
            if (isset($answerMap[$questionId])) {
                $value = $answerMap[$questionId];
                if (is_array($value)) {
                    $surveyAnswer->setAnswerArray($value);
                } else {
                    $surveyAnswer->setAnswer($value);
                }
            }
        }
    }
}
