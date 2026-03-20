<?php

namespace App\Interview\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Interview\Infrastructure\Entity\Interview;
use App\Interview\Infrastructure\Entity\InterviewSchema;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class AdminInterviewSchemaDeleteProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        // Provider already verified existence and returns 404 if not found
        $id = $uriVariables['id'] ?? null;
        $schema = $this->em->getRepository(InterviewSchema::class)->find($id);

        if ($schema === null) {
            return;
        }

        // Check if any interviews reference this schema
        $linkedInterviews = $this->em->getRepository(Interview::class)->findBy(['interviewSchema' => $schema]);
        if (count($linkedInterviews) > 0) {
            throw new ConflictHttpException('Cannot delete interview schema: it is referenced by existing interviews.');
        }

        $this->em->remove($schema);
        $this->em->flush();
    }
}
