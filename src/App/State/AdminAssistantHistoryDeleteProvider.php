<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\AdminAssistantHistoryDeleteResource;
use App\Entity\AssistantHistory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminAssistantHistoryDeleteProvider implements ProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AdminAssistantHistoryDeleteResource
    {
        $id = $uriVariables['id'] ?? null;
        $assistantHistory = $id ? $this->em->getRepository(AssistantHistory::class)->find($id) : null;

        if ($assistantHistory === null) {
            throw new NotFoundHttpException('Assistant history not found.');
        }

        $resource = new AdminAssistantHistoryDeleteResource();
        $resource->id = $assistantHistory->getId();

        return $resource;
    }
}
