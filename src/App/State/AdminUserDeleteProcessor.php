<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class AdminUserDeleteProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Security $security,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $id = $uriVariables['id'] ?? null;
        $user = $this->em->getRepository(User::class)->find($id);

        if ($user === null) {
            return;
        }

        $currentUser = $this->security->getUser();
        if (!$currentUser instanceof User) {
            return;
        }

        // Cannot delete self
        if ($user->getId() === $currentUser->getId()) {
            throw new ConflictHttpException('Du kan ikke slette deg selv.');
        }

        // ADMIN can delete anyone; others can only delete users in same department
        if (!$this->security->isGranted('ROLE_ADMIN')) {
            $userDept = $user->getFieldOfStudy()?->getDepartment();
            $currentDept = $currentUser->getFieldOfStudy()?->getDepartment();
            if ($userDept === null || $currentDept === null || $userDept->getId() !== $currentDept->getId()) {
                throw new AccessDeniedHttpException('You can only delete users in your own department.');
            }
        }

        $this->em->remove($user);
        $this->em->flush();
    }
}
