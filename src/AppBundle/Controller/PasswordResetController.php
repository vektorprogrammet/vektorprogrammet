<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\NewPasswordType;
use AppBundle\Form\Type\PasswordResetType;

/**
 * Class PasswordResetController.
 */
class PasswordResetController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * Shows the request new password page
     */
    public function showAction(Request $request)
    {
        //Creates new PasswordResetType Form
        $form = $this->createForm(new PasswordResetType());

        $form->handleRequest($request);

        //Checks if the form is valid
        if ($form->isValid()) {
            $passwordReset = $this->get('app.password_manager')->createResetPasswordEntity($form->get('email')->getData());

            if ($passwordReset === null) {
                $this->get('session')->getFlashBag()->add('errorMessage', '<em>Det finnes ingen brukere med denne e-postadressen</em>');
            } else {
                $oldPasswordResets = $this->getDoctrine()->getRepository('AppBundle:PasswordReset')->findByUser($passwordReset->getUser());
                $em = $this->getDoctrine()->getManager();

                foreach ($oldPasswordResets as $oldPasswordReset) {
                    $em->remove($oldPasswordReset);
                }

                $em->persist($passwordReset);
                $em->flush();

                $this->get('app.password_manager')->sendResetCode($passwordReset);

                return $this->redirectToRoute('reset_password_confirmation');
            }
        }
        //Render reset_password twig with the form.
        return $this->render('reset_password/reset_password.html.twig', array('form' => $form->createView()));
    }

    public function showConfirmationAction()
    {
        return $this->render('reset_password/confirmation.html.twig');
    }

    /**
     * @param $resetCode
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * This function resets stores the new password when the user goes to the url for resetting the password
     */
    public function resetPasswordAction($resetCode, Request $request)
    {
        $passwordManager = $this->get('app.password_manager');

        if (!$passwordManager->resetCodeIsValid($resetCode) || $passwordManager->resetCodeHasExpired($resetCode)) {
            return $this->render('error/error_message.html.twig', array(
                'title' => 'Ugyldig kode',
                'message' => "Koden er ugyldig eller utløpt. Gå til <a href='{$this->generateUrl('reset_password')}'>Glemt passord?</a> for å få tilsendt ny link.",
            ));
        }

        $passwordReset = $passwordManager->getPasswordResetByResetCode($resetCode);
        $user = $passwordReset->getUser();

        $form = $this->createForm(new NewPasswordType(), $user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($passwordReset);
            $em->persist($user);
            $em->flush();

            //renders the login page, with a feedback message so that the user knows that the new password was stored.
            $feedback = 'Logg inn med ditt nye passord';

            return $this->render('login/login.html.twig', array('message' => $feedback, 'error' => null, 'last_username' => $user->getUsername()));
        }

        return $this->render('reset_password/new_password.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
