<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\ProfileResource;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;

class ProfileProvider implements ProviderInterface
{
    public function __construct(
        private readonly Security $security,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ProfileResource
    {
        /** @var User $user */
        $user = $this->security->getUser();

        return self::fromUser($user);
    }

    public static function fromUser(User $user): ProfileResource
    {
        $profile = new ProfileResource();
        $profile->id = $user->getId();
        $profile->firstName = $user->getFirstName();
        $profile->lastName = $user->getLastName();
        $profile->userName = $user->getUserName();
        $profile->email = $user->getEmail();
        $profile->phone = $user->getPhone();
        $profile->gender = $user->getGender();

        $fos = $user->getFieldOfStudy();
        if ($fos) {
            $profile->fieldOfStudy = [
                'id' => $fos->getId(),
                'name' => $fos->getName(),
                'shortName' => $fos->getShortName(),
            ];
        }

        return $profile;
    }
}
