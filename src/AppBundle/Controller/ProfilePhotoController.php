<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Role\Roles;
use AppBundle\Service\FileUploader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProfilePhotoController extends BaseController
{
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

        $picturePath = $this->get(FileUploader::class)->uploadProfileImage($request);
        if (!$picturePath) {
            return new JsonResponse("Kunne ikke laste inn bildet", 400);
        }

        $this->get(FileUploader::class)->deleteProfileImage($user->getPicturePath());
        $user->setPicturePath($picturePath);

        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse("Upload OK");
    }
}
