<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Role\Roles;
use AppBundle\Service\Contract\FileUploaderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ProfilePhotoController extends BaseController
{
    private $fileUploader;
    private $entityManager;

    /**
     * @param FileUploaderInterface $fileUploader
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        FileUploaderInterface $fileUploader,
        EntityManagerInterface $entityManager
    ) {
        $this->fileUploader = $fileUploader;
        $this->entityManager = $entityManager;
    }
    public function showEditProfilePhotoAction(User $user)
    {
        $loggedInUser = $this->getUser();
        if ($user !== $loggedInUser && !$this->isGranted(Roles::TEAM_LEADER)) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('profile/edit_profile_photo.html.twig', array(
            'user' => $user,
        ));
    }

    public function editProfilePhotoUploadAction(User $user, Request $request)
    {
        $loggedInUser = $this->getUser();
        if ($user !== $loggedInUser && !$this->isGranted(Roles::TEAM_LEADER)) {
            throw $this->createAccessDeniedException();
        }

        $picturePath = $this->fileUploader->uploadProfileImage($request);
        if (!$picturePath) {
            return new JsonResponse("Kunne ikke laste inn bildet", 400);
        }

        $this->fileUploader->deleteProfileImage($user->getPicturePath());
        $user->setPicturePath($picturePath);

        $this->entityManager->flush();

        return new JsonResponse("Upload OK");
    }
}
