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
    public function showAction()
    {
        // Get the user currently signed in
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        // Fetch the assistant history of the user
        $assistantHistory = $em->getRepository('AppBundle:AssistantHistory')->findByUser($user);

        // Find the work history of the user
        $workHistory = $em->getRepository('AppBundle:WorkHistory')->findByUser($user);

        // Render the view
        return $this->render('profile/profile.html.twig', array(
            'user' => $user,
            'assistantHistory' => $assistantHistory,
            'workHistory' => $workHistory,
        ));
    }

    public function showSpecificProfileAction(User $user)
    {
        // If the user clicks their own public profile redirect them to their own profile site
        if ($user === $this->getUser()) {
            return $this->redirectToRoute('profile');
        }

        $em = $this->getDoctrine()->getManager();

        // Fetch the assistant history of the user
        $assistantHistory = $em->getRepository('AppBundle:AssistantHistory')->findByUser($user);

        // Find the work history of the user
        $workHistory = $em->getRepository('AppBundle:WorkHistory')->findByUser($user);

        return $this->render('profile/public_profile.html.twig', array(
            'user' => $user,
            'assistantHistory' => $assistantHistory,
            'workHistory' => $workHistory,
        ));
    }

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
            'department' => $user->getDepartment(),
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
}
