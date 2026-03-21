<?php

namespace App\Survey\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Identity\Infrastructure\AccessControlService;
use App\Identity\Infrastructure\Entity\User;
use App\Survey\Api\Resource\AdminSurveyWriteResource;
use App\Shared\Repository\SemesterRepository;
use App\Survey\Infrastructure\Repository\SurveyRepository;
use App\Survey\Infrastructure\Entity\SurveyQuestion;
use App\Survey\Infrastructure\Entity\SurveyQuestionAlternative;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class AdminSurveyEditProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SurveyRepository $surveyRepository,
        private readonly SemesterRepository $semesterRepository,
        private readonly Security $security,
        private readonly AccessControlService $accessControlService,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): AdminSurveyWriteResource
    {
        \assert($data instanceof AdminSurveyWriteResource);

        $id = $uriVariables['id'] ?? null;
        $survey = $id !== null ? $this->surveyRepository->find($id) : null;

        if ($survey === null) {
            throw new NotFoundHttpException('Survey not found.');
        }

        $currentUser = $this->security->getUser();
        $surveyDepartment = $survey->getDepartment();
        if ($surveyDepartment !== null && $currentUser instanceof User) {
            $this->accessControlService->assertDepartmentAccess($surveyDepartment, $currentUser);
        }

        if ($data->name !== null && $data->name !== '') {
            $survey->setName($data->name);
        } elseif ($data->name === '') {
            throw new UnprocessableEntityHttpException('Name cannot be blank.');
        }

        if ($data->semesterId !== null) {
            $semester = $this->semesterRepository->find($data->semesterId);
            if ($semester !== null) {
                $survey->setSemester($semester);
            }
        }

        if ($data->departmentId !== null) {
            $department = $this->em->getRepository(\App\Organization\Infrastructure\Entity\Department::class)->find($data->departmentId);
            if ($department !== null) {
                $survey->setDepartment($department);
            }
        }

        if ($data->targetAudience !== null) {
            $survey->setTargetAudience($data->targetAudience);
        }

        if ($data->confidential !== null) {
            $survey->setConfidential($data->confidential);
        }

        if ($data->finishPageContent !== null) {
            $survey->setFinishPageContent($data->finishPageContent);
        }

        if ($data->showCustomPopUpMessage !== null) {
            $survey->setShowCustomPopUpMessage($data->showCustomPopUpMessage);
        }

        if ($data->surveyPopUpMessage !== null) {
            $survey->setSurveyPopUpMessage($data->surveyPopUpMessage);
        }

        if ($data->questions !== null) {
            // Clear existing questions (ManyToMany: unlinks from join table)
            $survey->getSurveyQuestions()->clear();

            // Add new questions
            foreach ($data->questions as $questionData) {
                $question = new SurveyQuestion();
                $question->setQuestion($questionData['question']);
                $question->setType($questionData['type']);
                $question->setOptional($questionData['optional'] ?? false);
                if (isset($questionData['help'])) {
                    $question->setHelp($questionData['help']);
                }

                if (isset($questionData['alternatives'])) {
                    foreach ($questionData['alternatives'] as $altText) {
                        $alt = new SurveyQuestionAlternative();
                        $alt->setAlternative($altText);
                        $question->addAlternative($alt);
                    }
                }

                $this->em->persist($question);
                $survey->addSurveyQuestion($question);
            }
        }

        $this->em->persist($survey);
        $this->em->flush();

        $result = new AdminSurveyWriteResource();
        $result->id = $survey->getId();
        $result->name = $survey->getName();

        return $result;
    }
}
