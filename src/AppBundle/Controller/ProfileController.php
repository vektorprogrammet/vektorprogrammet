<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AssistantHistory;
use AppBundle\Entity\ExecutiveBoardMembership;
use AppBundle\Entity\Role;
use AppBundle\Entity\Signature;
use AppBundle\Entity\TeamMembership;
use AppBundle\Entity\User;
use AppBundle\Event\UserEvent;
use AppBundle\Form\Type\EditUserPasswordType;
use AppBundle\Form\Type\EditUserType;
use AppBundle\Form\Type\NewUserType;
use AppBundle\Form\Type\UserCompanyEmailType;
use AppBundle\Role\Roles;
use AppBundle\Service\Contract\LogServiceInterface;
use AppBundle\Service\Contract\ProfileServiceInterface;
use AppBundle\Service\Contract\RoleManagerInterface;
use AppBundle\Service\Contract\UserRegistrationInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class ProfileController extends BaseController
{
    private $entityManager;
    private $roleManager;
    private $userRegistration;
    private $logService;
    private $eventDispatcher;
    private $tokenStorage;
    private $session;
    private $kernel;
    private $profileService;

    /**
     * @param EntityManagerInterface $entityManager
     * @param RoleManagerInterface $roleManager
     * @param UserRegistrationInterface $userRegistration
     * @param LogServiceInterface $logService
     * @param EventDispatcherInterface $eventDispatcher
     * @param TokenStorageInterface $tokenStorage
     * @param SessionInterface $session
     * @param KernelInterface $kernel
     * @param ProfileServiceInterface $profileService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        RoleManagerInterface $roleManager,
        UserRegistrationInterface $userRegistration,
        LogServiceInterface $logService,
        EventDispatcherInterface $eventDispatcher,
        TokenStorageInterface $tokenStorage,
        SessionInterface $session,
        KernelInterface $kernel,
        ProfileServiceInterface $profileService
    ) {
        $this->entityManager = $entityManager;
        $this->roleManager = $roleManager;
        $this->userRegistration = $userRegistration;
        $this->logService = $logService;
        $this->eventDispatcher = $eventDispatcher;
        $this->tokenStorage = $tokenStorage;
        $this->session = $session;
        $this->kernel = $kernel;
        $this->profileService = $profileService;
    }
    public function showAction()
    {
        $user = $this->getUser();
        $profileData = $this->profileService->getProfileData($user);

        return $this->render('profile/profile.html.twig', $profileData);
    }

    public function showSpecificProfileAction(User $user)
    {
        // If the user clicks their own public profile redirect them to their own profile site
        if ($user === $this->getUser()) {
            return $this->redirectToRoute('profile');
        }

        $isGrantedAssistant = ($this->getUser() !== null && $this->roleManager->userIsGranted($this->getUser(), Roles::ASSISTANT));

        if (!$this->profileService->canViewProfile($user, $this->getUser(), $isGrantedAssistant)) {
            throw $this->createAccessDeniedException();
        }

        $profileData = $this->profileService->getProfileData($user);

        return $this->render('profile/profile.html.twig', $profileData);
    }

    public function deactivateUserAction(User $user)
    {
        $this->profileService->deactivateUser($user);

        return $this->redirectToRoute('specific_profile', ['id' => $user->getId()]);
    }

    public function activateUserAction(User $user)
    {
        $this->profileService->activateUser($user);

        return $this->redirectToRoute('specific_profile', ['id' => $user->getId()]);
    }

    public function activateNewUserAction(Request $request, $newUserCode)
    {
        $user = $this->userRegistration->activateUserByNewUserCode($newUserCode);

        if ($user === null) {
            return $this->render('error/error_message.html.twig', array(
                'title'   => 'Koden er ugyldig',
                'message' => 'Ugyldig kode eller brukeren er allerede opprettet',
            ));
        }

        $form = $this->createForm(NewUserType::class, $user, array(
            'validation_groups' => array( 'username' ),
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $token = new UsernamePasswordToken($user, null, 'secured_area', $user->getRoles());
            $this->tokenStorage->setToken($token);
            $this->session->set('_security_secured_area', serialize($token));

            $this->logService->info("User $user activated with new user code");

            return $this->redirectToRoute('my_page');
        }

        return $this->render('new_user/create_new_user.html.twig', array(
            'form' => $form->createView(),
            'user' => $user,
        ));
    }

    public function changeRoleAction(Request $request, User $user)
    {
        $response = array();

        $roleName = $this->roleManager->mapAliasToRole($request->request->get('role'));

        if (! $this->roleManager->loggedInUserCanChangeRoleOfUsersWithRole($user, $roleName)) {
            throw new BadRequestHttpException();
        }

        try {
            $role = $this->entityManager->getRepository(Role::class)->findByRoleName($roleName);
            $user->setRoles(array( $role ));

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $response['success'] = true;
        } catch (Exception $e) {
            $response['success'] = false;

            $response['cause'] = 'Kunne ikke endre rettighetsnivå'; // if you want to see the exception message.
        }

        // Send a response to ajax
        return new JsonResponse($response);
    }

    public function downloadCertificateAction(Request $request, User $user)
    {
        $signer = $this->getUser();
        $certificateData = $this->profileService->getCertificateData($user, $signer, $this->kernel->getProjectDir());

        $html = $this->renderView('certificate/certificate.html.twig', $certificateData);
        
        $this->profileService->generateCertificatePdf($html, 'attest.pdf');

        return null;
    }

    public function editProfileInformationAction(Request $request)
    {
        $user            = $this->getUser();
        $oldCompanyEmail = $user->getCompanyEmail();

        $form = $this->createForm(EditUserType::class, $user, array(
            'department'        => $user->getDepartment(),
            'validation_groups' => array( 'edit_user' ),
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->eventDispatcher->dispatch(UserEvent::EDITED, new UserEvent($user, $oldCompanyEmail));

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

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->redirect($this->generateUrl('profile'));
        }

        return $this->render('profile/edit_profile_password.html.twig', array(
            'form' => $form->createView(),
            'user' => $user,
        ));
    }

    public function editProfileInformationAdminAction(Request $request, User $user)
    {
        $form            = $this->createForm(EditUserType::class, $user, array(
            'department' => $user->getDepartment(),
        ));
        $oldCompanyEmail = $user->getCompanyEmail();

        // Handle the form
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->eventDispatcher->dispatch(UserEvent::EDITED, new UserEvent($user, $oldCompanyEmail));

            return $this->redirect($this->generateUrl('specific_profile', array( 'id' => $user->getId() )));
        }

        return $this->render('profile/edit_profile.html.twig', array(
            'form' => $form->createView(),
            'user' => $user,
        ));
    }

    public function editCompanyEmailAction(Request $request, User $user)
    {
        $oldCompanyEmail = $user->getCompanyEmail();
        $form            = $this->createForm(UserCompanyEmailType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->eventDispatcher->dispatch(UserEvent::COMPANY_EMAIL_EDITED, new UserEvent($user, $oldCompanyEmail));

            return $this->redirectToRoute('specific_profile', [ 'id' => $user->getId() ]);
        }

        return $this->render('profile/edit_company_email.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
