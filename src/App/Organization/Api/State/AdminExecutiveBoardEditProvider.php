<?php

declare(strict_types=1);

namespace App\Organization\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Organization\Api\Resource\AdminExecutiveBoardWriteResource;
use App\Organization\Infrastructure\Repository\ExecutiveBoardRepository;

class AdminExecutiveBoardEditProvider implements ProviderInterface
{
    public function __construct(
        private readonly ExecutiveBoardRepository $executiveBoardRepo,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AdminExecutiveBoardWriteResource
    {
        $board = $this->executiveBoardRepo->findBoard();

        $resource = new AdminExecutiveBoardWriteResource();
        $resource->id = $board->getId();
        $resource->name = $board->getName();
        $resource->description = $board->getDescription();
        $resource->shortDescription = $board->getShortDescription();

        return $resource;
    }
}
