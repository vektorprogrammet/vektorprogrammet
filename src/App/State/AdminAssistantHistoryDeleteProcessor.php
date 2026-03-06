<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\AssistantHistory;
use Doctrine\ORM\EntityManagerInterface;

class AdminAssistantHistoryDeleteProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $id = $uriVariables['id'] ?? null;
        $assistantHistory = $this->em->getRepository(AssistantHistory::class)->find($id);

        if ($assistantHistory === null) {
            return;
        }

        $this->em->remove($assistantHistory);
        $this->em->flush();
    }
}
