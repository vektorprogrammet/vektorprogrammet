<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\InterviewSchemaResource;
use App\Entity\InterviewSchema;
use Doctrine\ORM\EntityManagerInterface;

class InterviewSchemaListProvider implements ProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @return InterviewSchemaResource[]
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $schemas = $this->entityManager->getRepository(InterviewSchema::class)->findAll();

        return array_map(
            fn ($schema) => $this->toResource($schema),
            $schemas
        );
    }

    private function toResource(InterviewSchema $schema): InterviewSchemaResource
    {
        $resource = new InterviewSchemaResource();
        $resource->id = $schema->getId();
        $resource->name = $schema->getName();
        $resource->questionCount = $schema->getInterviewQuestions()->count();

        return $resource;
    }
}
