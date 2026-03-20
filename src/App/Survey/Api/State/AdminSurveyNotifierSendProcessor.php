<?php

namespace App\Survey\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Survey\Api\Resource\AdminSurveyNotifierSendResource;
use App\Survey\Infrastructure\Entity\SurveyNotificationCollection;
use App\Survey\Infrastructure\SurveyNotifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminSurveyNotifierSendProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SurveyNotifier $surveyNotifier,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): AdminSurveyNotifierSendResource
    {
        $id = $uriVariables['id'] ?? null;
        $collection = $id !== null ? $this->em->getRepository(SurveyNotificationCollection::class)->find($id) : null;

        if ($collection === null) {
            throw new NotFoundHttpException('SurveyNotificationCollection not found.');
        }

        if ($collection->getTimeOfNotification() > new \DateTime()) {
            throw new ConflictHttpException('Cannot send notifications before the scheduled time.');
        }

        if ($collection->isAllSent()) {
            throw new ConflictHttpException('All notifications have already been sent.');
        }

        $this->surveyNotifier->sendNotifications($collection);

        $result = new AdminSurveyNotifierSendResource();
        $result->id = $collection->getId();
        $result->success = $collection->isAllSent();

        return $result;
    }
}
