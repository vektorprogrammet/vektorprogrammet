<?php

declare(strict_types=1);

namespace App\Organization\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Organization\Api\Resource\AdminFieldOfStudyWriteResource;
use App\Organization\Infrastructure\Entity\FieldOfStudy;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminFieldOfStudyEditProvider implements ProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Security $security,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AdminFieldOfStudyWriteResource
    {
        $id = $uriVariables['id'] ?? null;
        $fieldOfStudy = $id !== null ? $this->em->getRepository(FieldOfStudy::class)->find($id) : null;

        if ($fieldOfStudy === null) {
            throw new NotFoundHttpException('Field of study not found.');
        }

        // Check department ownership
        /** @var \App\Identity\Infrastructure\Entity\User $user */
        $user = $this->security->getUser();
        $userDepartment = $user->getFieldOfStudy()->getDepartment();

        if ($fieldOfStudy->getDepartment() !== null && $fieldOfStudy->getDepartment()->getId() !== $userDepartment->getId()) {
            throw new AccessDeniedHttpException('You can only edit field of studies in your own department.');
        }

        $resource = new AdminFieldOfStudyWriteResource();
        $resource->id = $fieldOfStudy->getId();
        $resource->name = $fieldOfStudy->getName();
        $resource->shortName = $fieldOfStudy->getShortName();

        return $resource;
    }
}
