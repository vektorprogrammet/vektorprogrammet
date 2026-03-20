<?php

namespace App\Survey\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Survey\Infrastructure\Entity\SurveyNotificationCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminSurveyNotifierDeleteProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $id = $uriVariables['id'] ?? null;
        $collection = $id ? $this->em->getRepository(SurveyNotificationCollection::class)->find($id) : null;

        if ($collection === null) {
            throw new NotFoundHttpException('SurveyNotificationCollection not found.');
        }

        if ($collection->isActive()) {
            throw new ConflictHttpException('Cannot delete an active notification collection.');
        }

        $this->em->remove($collection);
        $this->em->flush();
    }
}
