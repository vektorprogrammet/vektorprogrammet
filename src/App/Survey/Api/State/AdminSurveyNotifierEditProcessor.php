<?php

namespace App\Survey\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Survey\Api\Resource\AdminSurveyNotifierWriteResource;
use App\Survey\Infrastructure\Entity\Survey;
use App\Survey\Infrastructure\Entity\SurveyNotificationCollection;
use App\Organization\Infrastructure\Entity\UserGroup;
use App\Survey\Infrastructure\SurveyNotifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminSurveyNotifierEditProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SurveyNotifier $surveyNotifier,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): AdminSurveyNotifierWriteResource
    {
        \assert($data instanceof AdminSurveyNotifierWriteResource);

        $id = $uriVariables['id'] ?? null;
        $collection = $id ? $this->em->getRepository(SurveyNotificationCollection::class)->find($id) : null;

        if ($collection === null) {
            throw new NotFoundHttpException('SurveyNotificationCollection not found.');
        }

        if ($collection->isActive()) {
            throw new ConflictHttpException('Cannot edit an active notification collection.');
        }

        if ($data->name !== null && $data->name !== '') {
            $collection->setName($data->name);
        }

        if ($data->surveyId !== null) {
            $survey = $this->em->getRepository(Survey::class)->find($data->surveyId);
            if ($survey !== null) {
                $collection->setSurvey($survey);
            }
        }

        if ($data->timeOfNotification !== null) {
            $collection->setTimeOfNotification(new \DateTime($data->timeOfNotification));
        }

        if ($data->notificationType !== null) {
            $collection->setNotificationType($data->notificationType);
        }

        if ($data->smsMessage !== null) {
            $collection->setSmsMessage($data->smsMessage);
        }

        if ($data->emailFromName !== null) {
            $collection->setEmailFromName($data->emailFromName);
        }

        if ($data->emailSubject !== null) {
            $collection->setEmailSubject($data->emailSubject);
        }

        if ($data->emailMessage !== null) {
            $collection->setEmailMessage($data->emailMessage);
        }

        if ($data->emailEndMessage !== null) {
            $collection->setEmailEndMessage($data->emailEndMessage);
        }

        if ($data->emailType !== null) {
            $collection->setEmailType($data->emailType);
        }

        if ($data->userGroupIds !== null) {
            $userGroups = [];
            foreach ($data->userGroupIds as $ugId) {
                $ug = $this->em->getRepository(UserGroup::class)->find($ugId);
                if ($ug !== null) {
                    $userGroups[] = $ug;
                }
            }
            $collection->setUserGroups($userGroups);
        }

        $this->surveyNotifier->initializeSurveyNotifier($collection);

        $result = new AdminSurveyNotifierWriteResource();
        $result->id = $collection->getId();

        return $result;
    }
}
