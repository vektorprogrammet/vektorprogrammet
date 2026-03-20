<?php

namespace App\Identity\Controller;

use App\Support\Controller\BaseController;
use App\Admission\Infrastructure\Repository\ApplicationRepository;
use App\Organization\Infrastructure\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Identity\Domain\Roles;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends BaseController
{
    public function __construct(
        private readonly AuthenticationUtils $authenticationUtils,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly ApplicationRepository $applicationRepo,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    #[Route('/login', name: 'login_route', methods: ['GET'])]
    public function loginAction()
    {
        // get the login error if there is one
        $error = $this->authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $this->authenticationUtils->getLastUsername();

        return $this->render(
            'login/login.html.twig',
            [
                // last username entered by the user
                'last_username' => $lastUsername,
                'error' => $error,
            ]
        );
    }

    /**
     * @return RedirectResponse
     *
     * @throws NonUniqueResultException
     */
    #[Route('/login/redirect', name: 'login_redirect', methods: ['GET'])]
    public function loginRedirectAction()
    {
        if ($this->authorizationChecker->isGranted(Roles::TEAM_MEMBER)) {
            return $this->redirectToRoute('control_panel');
        } elseif ($this->applicationRepo->findActiveByUser($this->getUser())) {
            return $this->redirectToRoute('my_page');
        }

        return $this->redirectToRoute('profile');
    }

    #[Route('/login_check', name: 'login_check', methods: ['POST'])]
    public function loginCheckAction()
    {
        return $this->redirectToRoute('home');
    }
}
