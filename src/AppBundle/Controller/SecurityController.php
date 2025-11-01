<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Application;
use AppBundle\Repository\Contract\ApplicationRepositoryInterface;
use AppBundle\Role\Roles;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SecurityController extends BaseController
{
    private $authenticationUtils;
    private $authorizationChecker;
    private $applicationRepository;

    /**
     * @param AuthenticationUtils $authenticationUtils
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param ApplicationRepositoryInterface $applicationRepository
     */
    public function __construct(
        AuthenticationUtils $authenticationUtils,
        AuthorizationCheckerInterface $authorizationChecker,
        ApplicationRepositoryInterface $applicationRepository
    ) {
        $this->authenticationUtils = $authenticationUtils;
        $this->authorizationChecker = $authorizationChecker;
        $this->applicationRepository = $applicationRepository;
    }
    public function loginAction()
    {
        // get the login error if there is one
        $error = $this->authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $this->authenticationUtils->getLastUsername();

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
     * @return RedirectResponse
     * @throws NonUniqueResultException
     */
    public function loginRedirectAction()
    {
        if ($this->authorizationChecker->isGranted(Roles::TEAM_MEMBER)) {
            return $this->redirectToRoute('control_panel');
        } elseif ($this->applicationRepository->findActiveByUser($this->getUser())) {
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
