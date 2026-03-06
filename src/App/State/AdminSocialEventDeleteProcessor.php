<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\SocialEvent;
use Doctrine\ORM\EntityManagerInterface;

class AdminSocialEventDeleteProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $id = $uriVariables['id'] ?? null;
        $event = $this->em->getRepository(SocialEvent::class)->find($id);

        if ($event === null) {
            return;
        }

        $this->em->remove($event);
        $this->em->flush();
    }
}
