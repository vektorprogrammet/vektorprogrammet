<?php

namespace App\Survey\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Survey\Api\Resource\AdminSurveyNotifierSendResource;
use App\Survey\Infrastructure\Entity\SurveyNotificationCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminSurveyNotifierSendProvider implements ProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AdminSurveyNotifierSendResource
    {
        $id = $uriVariables['id'] ?? null;
        $collection = $id !== null ? $this->em->getRepository(SurveyNotificationCollection::class)->find($id) : null;

        if ($collection === null) {
            throw new NotFoundHttpException('SurveyNotificationCollection not found.');
        }

        $resource = new AdminSurveyNotifierSendResource();
        $resource->id = $collection->getId();

        return $resource;
    }
}
