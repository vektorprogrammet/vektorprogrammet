<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\AdminSemesterDeleteResource;
use App\Entity\Semester;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminSemesterDeleteProvider implements ProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AdminSemesterDeleteResource
    {
        $id = $uriVariables['id'] ?? null;
        $semester = $id ? $this->em->getRepository(Semester::class)->find($id) : null;

        if ($semester === null) {
            throw new NotFoundHttpException('Semester not found.');
        }

        $resource = new AdminSemesterDeleteResource();
        $resource->id = $semester->getId();

        return $resource;
    }
}
