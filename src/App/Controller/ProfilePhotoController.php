<?php

namespace App\Controller;

use App\Entity\Repository\DepartmentRepository;
use App\Entity\Repository\SemesterRepository;
use App\Entity\User;
use App\Role\Roles;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProfilePhotoController extends BaseController
{
    public function __construct(
        private FileUploader $fileUploader,
        private EntityManagerInterface $em,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
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

        $this->em->flush();

        return new JsonResponse("Upload OK");
    }
}
