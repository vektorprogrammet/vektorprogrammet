<?php

namespace App\Identity\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Identity\Api\Resource\ProfileResource;
use App\Identity\Infrastructure\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class ProfileProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ProfileResource
    {
        assert($data instanceof ProfileResource);

        /** @var User $user */
        $user = $this->security->getUser();

        $user->setFirstName($data->firstName);
        $user->setLastName($data->lastName);
        $user->setEmail($data->email);
        $user->setPhone($data->phone);
        $user->setGender($data->gender);

        $this->em->flush();

        return ProfileProvider::fromUser($user);
    }
}
