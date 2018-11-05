<?php

namespace AppBundle\Controller;

use AppBundle\Role\Roles;

class SecurityController extends BaseController
{
    public function loginAction()
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            'login/login.html.twig',
            array(
                // last username entered by the user
                'last_username' => $lastUsername,
                'error' => $error,
            )
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function loginRedirectAction()
    {
        if ($this->get('security.authorization_checker')->isGranted(Roles::TEAM_MEMBER)) {
            return $this->redirectToRoute('control_panel');
        } elseif ($this->getDoctrine()->getRepository('AppBundle:Application')->findActiveByUser($this->getUser())) {
            return $this->redirectToRoute('my_page');
        } else {
            return $this->redirectToRoute('profile');
        }
    }

    public function loginCheckAction()
    {
        return $this->redirectToRoute('home');
    }
}
