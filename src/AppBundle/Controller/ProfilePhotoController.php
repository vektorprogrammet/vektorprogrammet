<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProfilePhotoController extends Controller
{
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
}
