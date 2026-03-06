<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\AdminInterviewSchemaDeleteResource;
use App\Entity\InterviewSchema;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminInterviewSchemaDeleteProvider implements ProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AdminInterviewSchemaDeleteResource
    {
        $id = $uriVariables['id'] ?? null;
        $schema = $id ? $this->em->getRepository(InterviewSchema::class)->find($id) : null;

        if ($schema === null) {
            throw new NotFoundHttpException('Interview schema not found.');
        }

        $resource = new AdminInterviewSchemaDeleteResource();
        $resource->id = $schema->getId();

        return $resource;
    }
}
