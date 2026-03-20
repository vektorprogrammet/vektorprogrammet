<?php

namespace App\Identity\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Organization\Infrastructure\Entity\FieldOfStudy;
use App\Identity\Infrastructure\Repository\RoleRepository;
use App\Identity\Infrastructure\Entity\User;
use App\Identity\Domain\Roles;
use App\Identity\Infrastructure\UserRegistration;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class AdminUserCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly RoleRepository $roleRepo,
        private readonly UserRegistration $userRegistration,
        private readonly Security $security,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $currentUser = $this->security->getUser();
        if (!$currentUser instanceof User) {
            throw new \RuntimeException('User not found in security context.');
        }

        $user = new User();
        $user->setFirstName($data->firstName);
        $user->setLastName($data->lastName);
        $user->setEmail($data->email);
        $user->setPhone($data->phone);
        $user->setGender(0);

        // Resolve field of study
        if ($data->fieldOfStudyId !== null) {
            $fieldOfStudy = $this->em->getRepository(FieldOfStudy::class)->find($data->fieldOfStudyId);
            if ($fieldOfStudy === null) {
                throw new UnprocessableEntityHttpException('Field of study not found.');
            }
            $user->setFieldOfStudy($fieldOfStudy);
        } else {
            // Default to current user's field of study
            $user->setFieldOfStudy($currentUser->getFieldOfStudy());
        }

        // Add ROLE_ASSISTANT (ROLE_USER)
        $role = $this->roleRepo->findByRoleName(Roles::ASSISTANT);
        $user->addRole($role);

        $this->em->persist($user);
        $this->em->flush();

        // Send activation email
        try {
            $this->userRegistration->sendActivationCode($user);
        } catch (\Throwable $e) {
            $this->logger->error('Failed to send activation email: '.$e->getMessage());
        }

        return ['id' => $user->getId()];
    }
}
