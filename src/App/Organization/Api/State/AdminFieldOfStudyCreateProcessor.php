<?php

declare(strict_types=1);

namespace App\Organization\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Organization\Infrastructure\Entity\FieldOfStudy;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class AdminFieldOfStudyCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Security $security,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): array
    {
        /** @var \App\Identity\Infrastructure\Entity\User $user */
        $user = $this->security->getUser();

        $fieldOfStudy = new FieldOfStudy();
        $fieldOfStudy->setName($data->name);
        $fieldOfStudy->setShortName($data->shortName);
        $fieldOfStudy->setDepartment($user->getFieldOfStudy()->getDepartment());

        $this->em->persist($fieldOfStudy);
        $this->em->flush();

        return ['id' => $fieldOfStudy->getId()];
    }
}
