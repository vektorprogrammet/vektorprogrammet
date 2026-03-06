<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\AdminSurveyWriteResource;
use App\Entity\Repository\SemesterRepository;
use App\Entity\Survey;
use App\Entity\SurveyQuestion;
use App\Entity\SurveyQuestionAlternative;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class AdminSurveyCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SemesterRepository $semesterRepository,
        private readonly Security $security,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): AdminSurveyWriteResource
    {
        \assert($data instanceof AdminSurveyWriteResource);

        $semesterId = $data->semesterId;
        if ($semesterId === null) {
            throw new UnprocessableEntityHttpException('semesterId is required.');
        }

        $semester = $this->semesterRepository->find($semesterId);
        if ($semester === null) {
            throw new UnprocessableEntityHttpException('Semester not found.');
        }

        $user = $this->security->getUser();
        if (!$user instanceof \App\Entity\User) {
            throw new AccessDeniedHttpException('Authentication required.');
        }

        $survey = new Survey();
        $survey->setName($data->name);
        $survey->setSemester($semester);
        $survey->setDepartment($user->getDepartment());

        if ($data->departmentId !== null) {
            $department = $this->em->getRepository(\App\Entity\Department::class)->find($data->departmentId);
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
            foreach ($data->questions as $questionData) {
                $question = new SurveyQuestion();
                $question->setQuestion($questionData['question'] ?? '');
                $question->setType($questionData['type'] ?? 'text');
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
