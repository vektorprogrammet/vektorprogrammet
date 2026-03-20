<?php

declare(strict_types=1);

namespace App\Organization\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Organization\Infrastructure\Entity\FieldOfStudy;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminFieldOfStudyEditProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $id = $uriVariables['id'] ?? null;
        $fieldOfStudy = $this->em->getRepository(FieldOfStudy::class)->find($id);

        if ($fieldOfStudy === null) {
            throw new NotFoundHttpException('Field of study not found.');
        }

        $fieldOfStudy->setName($data->name);
        $fieldOfStudy->setShortName($data->shortName);

        $this->em->persist($fieldOfStudy);
        $this->em->flush();

        return ['id' => $fieldOfStudy->getId()];
    }
}
