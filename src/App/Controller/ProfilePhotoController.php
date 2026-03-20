<?php

namespace App\Controller;

use App\Entity\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Entity\User;
use App\Role\Roles;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ProfilePhotoController extends BaseController
{
    public function __construct(
        private readonly FileUploader $fileUploader,
        private readonly EntityManagerInterface $em,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    #[Route('/profil/rediger/profilbilde/{id}', name: 'profile_edit_photo', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function showEditProfilePhotoAction(User $user)
    {
        $loggedInUser = $this->getUser();
        if ($user !== $loggedInUser && !$this->isGranted(Roles::TEAM_LEADER)) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('profile/edit_profile_photo.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/profil/rediger/profilbilde/upload/{id}', name: 'profile_upload_photo', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function editProfilePhotoUploadAction(User $user, Request $request)
    {
        $loggedInUser = $this->getUser();
        if ($user !== $loggedInUser && !$this->isGranted(Roles::TEAM_LEADER)) {
            throw $this->createAccessDeniedException();
        }

        $picturePath = $this->fileUploader->uploadProfileImage($request);
        if (!$picturePath) {
            return new JsonResponse('Kunne ikke laste inn bildet', 400);
        }

        $this->fileUploader->deleteProfileImage($user->getPicturePath());
        $user->setPicturePath($picturePath);

        $this->em->flush();

        return new JsonResponse('Upload OK');
    }
}
