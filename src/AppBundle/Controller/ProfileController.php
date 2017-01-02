<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\Type\NewUserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\EditUserType;
use AppBundle\Form\Type\EditUserPasswordType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ProfileController extends Controller
{
    public function deactivateUserAction(User $user)
    {
        try {
            // set user active level
            $user->setActive(0);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // Send a response back to AJAX
            $response['success'] = true;
        } catch (\Exception $e) {
            // Send a response back to AJAX
            $response['success'] = false;
            $response['cause'] = 'Kunne ikke endre rettighetene.';
        }

        // Send a response to ajax
        return new JsonResponse($response);
    }

    public function activateUserAction(User $user)
    {
        try {
            // set user active level
            $user->setActive(1);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // Send a response back to AJAX
            $response['success'] = true;
        } catch (\Exception $e) {
            // Send a response back to AJAX
            $response['success'] = false;
            $response['cause'] = 'Kunne ikke endre rettighetene.';
        }

        // Send a response to ajax
        return new JsonResponse($response);
    }

    public function activateNewUserAction(Request $request, $newUserCode)
    {
        $user = $this->get('app.user.registration')->activateUserByNewUserCode($newUserCode);

        if ($user === null) {
            return $this->render('error/error_message.html.twig', array(
                'title' => 'Koden er ugyldig',
                'message' => 'Ugyldig kode eller brukeren er allerede opprettet',
            ));
        }

        $form = $this->createForm(NewUserType::class, $user, array(
            'validation_groups' => array('username'),
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('login_route');
        }

        return $this->render('new_user/create_new_user.html.twig', array(
            'form' => $form->createView(),
            'user' => $user,
        ));
    }

    public function changeRoleAction(Request $request, User $user)
    {
        $response = array();

        $roleManager = $this->get('app.roles');
        $roleName = $roleManager->mapAliasToRole($request->request->get('role'));

        if (!$roleManager->canChangeToRole($roleName)) {
            throw new BadRequestHttpException();
        }

        try {
            $role = $this->getDoctrine()->getRepository('AppBundle:Role')->findByRoleName($roleName);
            $user->setRoles(array($role));

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $response['success'] = true;
        } catch (\Exception $e) {
            $response['success'] = false;

            $response['cause'] = 'Kunne ikke endre rettighetsnivå'; // if you want to see the exception message.
        }

        // Send a response to ajax
        return new JsonResponse($response);
    }

    public function downloadCertificateAction(Request $request, User $user)
    {
        $em = $this->getDoctrine()->getManager();

        // Fetch the assistant history of the user
        $assistantHistory = $em->getRepository('AppBundle:AssistantHistory')->findByUser($user);

        // Find the work history of the user
        $workHistory = $em->getRepository('AppBundle:WorkHistory')->findByUser($user);

        $signature = $this->getDoctrine()->getRepository('AppBundle:Signature')->findByUser($this->getUser());

        if ($signature === null) {
            return $this->redirectToRoute('certificate_signature_picture_upload');
        }

        $html = $this->renderView('certificate/certificate.html.twig', array(
            'user' => $user,
            'assistantHistory' => $assistantHistory,
            'workHistory' => $workHistory,
            'signature' => $signature,
            'base_dir' => $this->get('kernel')->getRootDir().'/../www'.$request->getBasePath(),
        ));
        $mpdfService = $this->get('tfox.mpdfport');

        return $mpdfService->generatePdfResponse($html);
    }

    public function editProfileInformationAction(Request $request)
    {
        $user = $this->getUser();

        $form = $this->createForm(EditUserType::class, $user, array(
            'department' => $user->getDepartment(),
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('profile'));
        }

        return $this->render('profile/edit_profile.html.twig', array(
            'form' => $form->createView(),
            'user' => $user,
        ));
    }

    public function editProfilePasswordAction(Request $request)
    {
        $user = $this->getUser();

        $form = $this->createForm(EditUserPasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('profile'));
        }

        return $this->render('profile/edit_profile_password.html.twig', array(
            'form' => $form->createView(),
            'user' => $user,
        ));
    }

    public function editProfileInformationAdminAction(Request $request, User $user)
    {
        $form = $this->createForm(EditUserType::class, $user, array(
            'department' => $user->getDepartment()
        ));

        // Handle the form
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('specific_profile', array('id' => $user->getId())));
        }

        return $this->render('profile/edit_profile_admin.html.twig', array(
            'form' => $form->createView(),
            'user' => $user,
        ));
    }

    /**
     * Updates profile photo for logged in user.
     * Request must contain a file of mime type image/jpeg.
     * Moves the image file to the profile photos folder.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function editProfilePhotoUploadAction($id, Request $request)
    {
        if ($this->get('security.context')->isGranted('ROLE_USER')) {

            // Get the current user logged in or load the targeted user if editor is super_admin
            $user = $this->get('security.context')->getToken()->getUser();
            if ($id !== $user->getId() && $this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            } else {
                $id = $user->getId();
            }

            //Target folder for the profile photo
            $targetFolder = $this->container->getParameter('profile_photos').'/';
            //Get filetype
            $extension = explode('.', $request->files->get('img')->getClientOriginalName());
            $extension = $extension[count($extension) - 1];

            //Remove previously uploaded photos
            if (file_exists($targetFolder.$id.'_temp.jpg')) {
                unlink($targetFolder.$id.'_temp.jpg');
            } elseif (file_exists($targetFolder.$id.'_temp.jpeg')) {
                unlink($targetFolder.$id.'_temp.jpeg');
            }

            try {
                //Move the file to new temporary location
                $request->files->get('img')->move($targetFolder, $id.'_temp.'.$extension);

                //Return the new URL
                $response = ['success' => true,
                    'url' => $this->container->get('templating.helper.assets')->getUrl($targetFolder.$id.'_temp.'.$extension),
                ];
            } catch (\Exception $e) {
                $response = ['success' => false,
                    'code' => $e->getCode(),
                    'cause' => 'Det oppstod en feil under lagringen av bildet. Prøv igjen eller kontakt IT ansvarlig.',
                ];
            }
        } else {
            $response = ['success' => false,
                'cause' => 'Du har ikke rettigheter til dette!',
            ];
        }

        return new JsonResponse($response);
    }

    public function saveProfilePhotoAction($id, Request $request)
    {
        if ($this->get('security.context')->isGranted('ROLE_USER')) {
            // Get the current user logged in or load the targeted user if editor is super_admin
            $user = $this->get('security.context')->getToken()->getUser();
            if ($id !== $user->getId() && $this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
                $user = $em = $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->findUserById($id);
            } else {
                $id = $user->getId();
            }

            //Target folder for the profile photo
            $targetFolder = $this->container->getParameter('profile_photos').'/';
            $path = $targetFolder.$id.'.jpg';
            //Get path to edited and cropped image
            $oldPath = $targetFolder.$id.'_cropped.jpg';

            //Make sure the user have edited and cropped the image, if they have, set it as their new profile image
            if (file_exists($oldPath)) {
                rename($oldPath, $path);
            } elseif (file_exists($targetFolder.$id.'_temp.jpeg')) { //We dont have an edited version of the image, but the user have uploaded an image (jpeg)
                $this->addFlash('profile-notice', 'Har du husket å redigere bildet?');

                return $this->render('profile/edit_profile_photo.html.twig', array(
                    'path' => $this->get('request')->getBasePath().'/'.$targetFolder.$id.'_temp.jpeg',
                    'user' => $user,
                ));
            } elseif (file_exists($targetFolder.$id.'_temp.jpg')) { //We dont have an edited version of the image, but the user have uploaded an image (jpg)
                $this->addFlash('profile-notice', 'Har du husket å redigere bildet?');

                return $this->render('profile/edit_profile_photo.html.twig', array(
                    'path' => $this->get('request')->getBasePath().'/'.$targetFolder.$id.'_temp.jpg',
                    'user' => $user,
                ));
            } else { //No edited image or uploaded image
                $this->addFlash('profile-notice', 'Har du lastet opp et bilde?');

                return $this->redirect($this->generateUrl('profile_edit_photo', array('id' => $id)));
            }

            //Remove the old version of the photo if it exists (Is this necessary?)
            if (file_exists($targetFolder.$id.'_cropped.jpg')) {
                unlink($targetFolder.$id.'_cropped.jpg');
            }

            //Update the database
            $user->setPicturePath($path);
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($user);
            $em->flush();
            if ($user == $this->get('security.context')->getToken()->getUser()) {
                return $this->redirect($this->generateUrl('profile'));
            } else {
                return $this->redirect($this->generateUrl('specific_profile', array('id' => $id)));
            }
        } else {
            return $this->redirect($this->generateUrl('home'));
        }
    }

    /**
     * This method is intended to be called by an Ajax request.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function saveProfilePhotoEditorResponseAction($id, Request $request)
    {
        if ($this->get('security.context')->isGranted('ROLE_USER')) {

            // Get the current user logged in or load the targeted user if editor is super_admin
            $user = $this->get('security.context')->getToken()->getUser();
            if ($id !== $user->getId() && $this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            } else {
                $id = $user->getId();
            }

            //Get the SDK url to the new picture
            $content = $request->getContent();
            $data = json_decode($content, true);
            $aviaryURL = $data['aviaryURL'];

            //Get path to where the new file will be stored
            $targetFolder = $this->container->getParameter('profile_photos').'/';
            $path = $targetFolder.$id.'_cropped.jpg';

            try {
                //copy the image to the new location
                copy($aviaryURL, $path);

                //Remove the original upload of the photo
                if (file_exists($targetFolder.$id.'_temp.jpg')) {
                    unlink($targetFolder.$id.'_temp.jpg');
                } elseif (file_exists($targetFolder.$id.'_temp.jpeg')) {
                    unlink($targetFolder.$id.'_temp.jpeg');
                }

                //Return the new URL
                $response = [
                    'success' => true,
                    'localURL' => $this->get('request')->getBasePath().'/'.$path,
                ];
            } catch (\Exception $e) {
                $response = ['success' => false,
                    'code' => $e->getCode(),
                    'cause' => 'Det oppstod en feil under lagringen av bildet. Prøv igjen eller kontakt IT ansvarlig.',
                ];
            }
        } else {
            $response = ['success' => false,
                'cause' => 'Du har ikke tilstrekkelige rettigheter',
            ];
        }

        return new JsonResponse($response);
    }

    public function showEditProfilePhotoAction($id)
    {
        if ($this->get('security.context')->isGranted('ROLE_USER')) {
            // Get the current user logged in or load the targeted user if editor is super_admin
            $user = $this->get('security.context')->getToken()->getUser();
            if ($id !== $user->getId() && $this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
                $user = $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->findUserById($id);
            } else {
                $id = $user->getId();
            }

            //Remove previously uploaded and edited images, make sure we start with clean sheets
            $targetFolder = $this->container->getParameter('profile_photos').'/';
            if (file_exists($targetFolder.$id.'_cropped.jpg')) {
                unlink($targetFolder.$id.'_cropped.jpg');
            }
            if (file_exists($targetFolder.$id.'_temp.jpg')) {
                unlink($targetFolder.$id.'_temp.jpg');
            }
            if (file_exists($targetFolder.$id.'_temp.jpeg')) {
                unlink($targetFolder.$id.'_temp.jpeg');
            }

            //If the user already have a profile picture, get the url and send it to the editor
            if ($user->getPicturePath() != 'images/defaultProfile.png') {
                $path = $user->getPicturePath();
                $path = $this->get('request')->getBasePath().'/'.$path;
            } else {
                $path = '';
            }

            return $this->render('profile/edit_profile_photo.html.twig', array(
                'path' => $path,
                'user' => $user,
            ));
        } else {
            return $this->redirect($this->generateUrl('home'));
        }
    }

    public function showSpecificProfileAction(Request $request)
    {
        if ($this->get('security.context')->isGranted('ROLE_USER')) {
            // Get the ID variable sent by the request
            $id = $this->getRequest()->get('id');

            $em = $this->getDoctrine()->getEntityManager();

            // Find the user with the ID equal to $id
            $user = $em->getRepository('AppBundle:User')->find($id);

            // Get the role of the user
            $roles = $user->getRoles();

            // Fetch the assistant history of the user
            $assistantHistory = $em->getRepository('AppBundle:AssistantHistory')->findByUser($user);

            // Find the work history of the user
            $workHistory = $em->getRepository('AppBundle:WorkHistory')->findByUser($user);

            // If the user clicks their own public profile redirect them to their own profile site
            if ($user == $this->get('security.context')->getToken()->getUser()) {
                return $this->redirect($this->generateUrl('profile'));
            } else {
                return $this->render('profile/public_profile.html.twig', array(
                    'user' => $user,
                    'assistantHistory' => $assistantHistory,
                    'workHistory' => $workHistory,
                    'roles' => $roles,
                ));
            }
        } else {
            return $this->redirect($this->generateUrl('home'));
        }
    }

    public function showAction()
    {
        if ($this->get('security.context')->isGranted('ROLE_USER')) {
            // Get the user currently signed in
            $user = $this->get('security.context')->getToken()->getUser();

            // Get the ID of the user logged in
            $id = $user->getId();

            $em = $this->getDoctrine()->getEntityManager();

            // Get the role of the user
            $roles = $user->getRoles();

            // Fetch the user object with the user ID from database
            $user = $em->getRepository('AppBundle:User')->find($id);

            // Fetch the assistant history of the user
            $assistantHistory = $em->getRepository('AppBundle:AssistantHistory')->findByUser($user);

            // A array with all the active schools
            $activeSchools = array();

            // Find the work history of the user
            $workHistory = $em->getRepository('AppBundle:WorkHistory')->findByUser($user);

            // Find any active assistant histories.. We use these to create a link on the profile page to the schools
            foreach ($assistantHistory as $as) {

                // Get the school of the assistant history
                $school = $as->getSchool();

                // Find active assistant histories
                $activeAssistantHistory = $this->getDoctrine()->getRepository('AppBundle:AssistantHistory')->findActiveAssistantHistoriesBySchool($school);

                if (in_array($as, $activeAssistantHistory)) {
                    $activeSchools[] = $school;
                }
            }

            // Render the view
            return $this->render('profile/profile.html.twig', array(
                'user' => $user,
                'assistantHistory' => $assistantHistory,
                'activeSchools' => $activeSchools,
                'workHistory' => $workHistory,
                'roles' => $roles,
            ));
        } else {
            return $this->redirect($this->generateUrl('home'));
        }
    }
}
