<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\PasswordReset;
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
        //Creates new PasswordReset entity
        $passwordReset = new PasswordReset();

        //Creates new PasswordResetType Form
        $form = $this->createForm(new PasswordResetType(), $passwordReset);

        $form->handleRequest($request);

        //Checks if the form is valid
        if ($form->isValid()) {
            //Creates a resetpassword-Entity and sends a reset url by Email to the user. if the username and email is correct
            if ($this->createResetPasswordEntity($form, $passwordReset)) {
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
     * @param $form
     * @param $passwordReset
     *
     * @return bool
     *
     * Creates a resetPassword field in the resetPassword entity, with a reset code, date and the user who want to reset the password.
     * The function sends a url to the user where the user can reset the password
     */
    private function createResetPasswordEntity($form, $passwordReset)
    {

        //Connects with the User Entity
        $repositoryUser = $this->getDoctrine()->getRepository('AppBundle:User');

        //Gets the email that is typed in the text-field
        $email = $form->get('email')->getData();

        //Finds the user based on the email
        $user = $repositoryUser->findUserByEmail($email);
        if ($user === null) {
            $this->get('session')->getFlashBag()->add('errorMessage', '<em>Det finnes ingen brukere med denne e-postadressen</em>');

            return false;
        }

        //Creates a random hex-string as reset code
        $resetCode = bin2hex(openssl_random_pseudo_bytes(12));

        //Hashes the random reset code to store in the database
        $hashedResetCode = hash('sha512', $resetCode, false);

        //creates a DateTime objekt for the table, this is to have a expiration time for the reset code
        $time = new \DateTime();

        //Delets old resetcodes from the database
        $repositoryPasswordReset = $this->getDoctrine()->getRepository('AppBundle:PasswordReset');
        $repositoryPasswordReset->deletePasswordResetsByUser($user);

        //Adds the info in the passwordReset entity
        $passwordReset->setUser($user);
        $passwordReset->setResetTime($time);
        $passwordReset->setHashedResetCode($hashedResetCode);
        $em = $this->getDoctrine()->getManager();
        $em->persist($passwordReset);
        $em->flush();

        //Sends a email with the url for resetting the password
        $emailMessage = \Swift_Message::newInstance()
            ->setSubject('Tilbakestill passord for vektorprogrammet.no')
            ->setFrom(array('ikkesvar@vektorprogrammet.no' => 'Vektorprogrammet'))
            ->setTo($email)
            ->setBody($this->renderView('reset_password/new_password_email.txt.twig', array(
                'resetCode' => $resetCode,
                'user' => $user,
            )));
        $this->get('mailer')->send($emailMessage);

        return true;
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

        //Creates a new newPasswordType form, and send in user so that it is the password for the correct user that is changed.
        $form = $this->createForm(new NewPasswordType(), $user);

        //Handles the request from the form
        $form->handleRequest($request);

        //checks if the form is valid(the information is stored correctly in the user object)
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
