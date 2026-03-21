<?php

namespace App\Identity\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Identity\Api\Resource\ProfileResource;
use App\Identity\Domain\Events\UserEvent;
use App\Identity\Infrastructure\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ProfileProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly EntityManagerInterface $em,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ProfileResource
    {
        assert($data instanceof ProfileResource);

        /** @var User $user */
        $user = $this->security->getUser();

        $oldEmail = $user->getEmail();

        $user->setFirstName($data->firstName);
        $user->setLastName($data->lastName);
        $user->setEmail($data->email);
        $user->setPhone($data->phone);
        $user->setGender((bool) $data->gender);

        $this->em->flush();

        $this->eventDispatcher->dispatch(new UserEvent($user, $oldEmail), UserEvent::EDITED);

        return ProfileProvider::fromUser($user);
    }
}
