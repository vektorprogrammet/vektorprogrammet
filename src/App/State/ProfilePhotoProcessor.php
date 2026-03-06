<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ProfilePhotoProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly EntityManagerInterface $em,
        private readonly FileUploader $fileUploader,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        /** @var User $user */
        $user = $this->security->getUser();

        $request = $this->requestStack->getCurrentRequest();
        if ($request === null) {
            throw new BadRequestHttpException('No request available');
        }

        $path = $this->fileUploader->uploadProfileImage($request);
        if (!$path) {
            throw new BadRequestHttpException('Could not upload image');
        }

        $oldPath = $user->getPicturePath();
        if ($oldPath) {
            $this->fileUploader->deleteProfileImage($oldPath);
        }

        $user->setPicturePath($path);

        $this->em->flush();
    }
}
