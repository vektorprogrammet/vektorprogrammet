<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Application;
use Doctrine\ORM\EntityManagerInterface;

class AdminApplicationDeleteProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        // Provider already verified existence and returns 404 if not found
        $id = $uriVariables['id'] ?? null;
        $application = $this->em->getRepository(Application::class)->find($id);

        if ($application !== null) {
            $this->em->remove($application);
            $this->em->flush();
        }
    }
}
