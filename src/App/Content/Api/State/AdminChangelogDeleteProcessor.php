<?php

namespace App\Content\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Content\Infrastructure\Entity\ChangeLogItem;
use Doctrine\ORM\EntityManagerInterface;

class AdminChangelogDeleteProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $id = $uriVariables['id'] ?? null;
        $item = $this->em->getRepository(ChangeLogItem::class)->find($id);

        if ($item === null) {
            return;
        }

        $this->em->remove($item);
        $this->em->flush();
    }
}
