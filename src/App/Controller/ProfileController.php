<?php

namespace App\Controller;

use App\Support\Controller\BaseController;
use App\Operations\Infrastructure\Repository\AssistantHistoryRepository;
use App\Organization\Infrastructure\Repository\DepartmentRepository;
use App\Organization\Infrastructure\Repository\ExecutiveBoardMembershipRepository;
use App\Entity\Repository\RoleRepository;
use App\Shared\Repository\SemesterRepository;
use App\Operations\Infrastructure\Repository\SignatureRepository;
use App\Organization\Infrastructure\Repository\TeamMembershipRepository;
use App\Operations\Infrastructure\Entity\Signature;
use App\Entity\User;
use App\Event\UserEvent;
use App\Form\Type\EditUserPasswordType;
use App\Form\Type\EditUserType;
use App\Form\Type\NewUserType;
use App\Form\Type\UserCompanyEmailType;
use App\Role\Roles;
use App\Support\Infrastructure\LogService;
use App\Service\RoleManager;
use App\Service\UserRegistration;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class ProfileController extends BaseController
{
    public function __construct(
        private readonly AssistantHistoryRepository $assistantHistoryRepo,
        private readonly TeamMembershipRepository $teamMembershipRepo,
        private readonly ExecutiveBoardMembershipRepository $executiveBoardMembershipRepo,
        private readonly RoleRepository $roleRepo,
        private readonly SignatureRepository $signatureRepo,
        private readonly UserRegistration $userRegistration,
        private readonly RoleManager $roleManager,
        private readonly LogService $logService,
        private readonly TokenStorageInterface $tokenStorage,
        private readonly RequestStack $requestStack,
        private readonly KernelInterface $kernel,
        private readonly EntityManagerInterface $em,
        private readonly EventDispatcherInterface $eventDispatcher,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    #[Route('/profile', name: 'profile', methods: ['GET'])]
    public function showAction()
    {
        // Get the user currently signed in
        $user = $this->getUser();

        // Fetch the assistant history of the user
        $assistantHistory = $this->assistantHistoryRepo->findByUser($user);

        // Find the team history of the user
        $teamMemberships = $this->teamMembershipRepo->findByUser($user);

        // Find the executive board history of the user
        $executiveBoardMemberships = $this->executiveBoardMembershipRepo->findByUser($user);

        // Render the view
        return $this->render('profile/profile.html.twig', [
            'user' => $user,
            'assistantHistory' => $assistantHistory,
            'teamMemberships' => $teamMemberships,
            'executiveBoardMemberships' => $executiveBoardMemberships,
        ]);
    }

    #[Route('/profile/{id}', name: 'specific_profile', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function showSpecificProfileAction(User $user)
    {
        // If the user clicks their own public profile redirect them to their own profile site
        if ($user === $this->getUser()) {
            return $this->redirectToRoute('profile');
        }

        // Find the work history of the user
        $teamMemberships = $this->teamMembershipRepo->findByUser($user);

        // Find the executive board history of the user
        $executiveBoardMemberships = $this->executiveBoardMembershipRepo->findByUser($user);

        $isGrantedAssistant = ($this->getUser() !== null && $this->roleManager->userIsGranted($this->getUser(), Roles::ASSISTANT));

        if (empty($teamMemberships) && empty($executiveBoardMemberships) && !$isGrantedAssistant) {
            throw $this->createAccessDeniedException();
        }

        // Fetch the assistant history of the user
        $assistantHistory = $this->assistantHistoryRepo->findByUser($user);

        // Render the view
        return $this->render('profile/profile.html.twig', [
            'user' => $user,
            'assistantHistory' => $assistantHistory,
            'teamMemberships' => $teamMemberships,
            'executiveBoardMemberships' => $executiveBoardMemberships,
        ]);
    }

    #[Route('/profile/deaktiv/{id}', name: 'profile_deactivate_user', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deactivateUserAction(User $user)
    {
        $user->setActive(false);

        $this->em->flush();

        return $this->redirectToRoute('specific_profile', ['id' => $user->getId()]);
    }

    #[Route('/profile/aktiv/{id}', name: 'profile_activate_user', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function activateUserAction(User $user)
    {
        $user->setActive(true);

        $this->em->flush();

        return $this->redirectToRoute('specific_profile', ['id' => $user->getId()]);
    }

    #[Route('/bruker/aktiver/{newUserCode}', name: 'profile_activate_with_user_code', methods: ['GET', 'POST'])]
    public function activateNewUserAction(Request $request, $newUserCode)
    {
        $user = $this->userRegistration->activateUserByNewUserCode($newUserCode);

        if ($user === null) {
            return $this->render('error/error_message.html.twig', [
                'title' => 'Koden er ugyldig',
                'message' => 'Ugyldig kode eller brukeren er allerede opprettet',
            ]);
        }

        $form = $this->createForm(NewUserType::class, $user, [
            'validation_groups' => ['username'],
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($user);
            $this->em->flush();

            $token = new UsernamePasswordToken($user, 'secured_area', $user->getRoles());
            $this->tokenStorage->setToken($token);
            $this->requestStack->getSession()->set('_security_secured_area', serialize($token));

            $this->logService->info("User $user activated with new user code");

            return $this->redirectToRoute('my_page');
        }

        return $this->render('new_user/create_new_user.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/profile/rolle/endre/{id}', name: 'profile_change_role', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function changeRoleAction(Request $request, User $user)
    {
        $response = [];

        $roleName = $this->roleManager->mapAliasToRole($request->request->get('role'));

        if (!$this->roleManager->loggedInUserCanChangeRoleOfUsersWithRole($user, $roleName)) {
            throw new BadRequestHttpException();
        }

        try {
            $role = $this->roleRepo->findByRoleName($roleName);
            $user->setRoles([$role]);

            $this->em->persist($user);
            $this->em->flush();

            $response['success'] = true;
        } catch (\Exception) {
            $response['success'] = false;

            $response['cause'] = 'Kunne ikke endre rettighetsnivå'; // if you want to see the exception message.
        }

        // Send a response to ajax
        return new JsonResponse($response);
    }

    #[Route('/profile/attest/{id}', name: 'profile_certificate', methods: ['GET'])]
    public function downloadCertificateAction(Request $request, User $user)
    {
        // Fetch the assistant history of the user
        $assistantHistory = $this->assistantHistoryRepo->findByUser($user);
        // Find the work history of the user
        $teamMembership = $this->teamMembershipRepo->findByUser($user);
        // Find the signature of the user creating the certificate
        $signature = $this->signatureRepo->findByUser($this->getUser());
        // Find department
        $department = $this->getUser()->getDepartment();
        // Find any additional comment
        $additional_comment = $signature->getAdditionalComment();

        if ($signature === null) {
            return $this->redirectToRoute('certificate_show');
        }

        $html = $this->renderView('certificate/certificate.html.twig', [
            'user' => $user,
            'assistantHistory' => $assistantHistory,
            'teamMembership' => $teamMembership,
            'signature' => $signature,
            'additional_comment' => $additional_comment,
            'department' => $department,
            'base_dir' => $this->kernel->getProjectDir().'/web',
        ]);
        $options = new Options();
        $options->setIsRemoteEnabled(true);
        $options->setChroot('/../');

        $dompdf = new Dompdf($options);
        $dompdf->setPaper('A4');

        $html = preg_replace('/>\s+</', '><', $html);
        $dompdf->loadHtml($html);

        $dompdf->render();

        $dompdf->stream($filename = 'attest.pdf');

        return null;
    }

    #[Route('/profil/rediger', name: 'profile_edit', methods: ['GET', 'POST'])]
    public function editProfileInformationAction(Request $request)
    {
        $user = $this->getUser();
        $oldCompanyEmail = $user->getCompanyEmail();

        $form = $this->createForm(EditUserType::class, $user, [
            'department' => $user->getDepartment(),
            'validation_groups' => ['edit_user'],
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($user);
            $this->em->flush();

            $this->eventDispatcher->dispatch(new UserEvent($user, $oldCompanyEmail), UserEvent::EDITED);

            return $this->redirect($this->generateUrl('profile'));
        }

        return $this->render('profile/edit_profile.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/profil/rediger/passord/', name: 'profile_edit_password', methods: ['GET', 'POST'])]
    public function editProfilePasswordAction(Request $request)
    {
        $user = $this->getUser();

        $form = $this->createForm(EditUserPasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($user);
            $this->em->flush();

            return $this->redirect($this->generateUrl('profile'));
        }

        return $this->render('profile/edit_profile_password.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/kontrollpanel/profil/rediger/{id}', name: 'profile_edit_admin', methods: ['GET', 'POST'])]
    public function editProfileInformationAdminAction(Request $request, User $user)
    {
        $form = $this->createForm(EditUserType::class, $user, [
            'department' => $user->getDepartment(),
        ]);
        $oldCompanyEmail = $user->getCompanyEmail();

        // Handle the form
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($user);
            $this->em->flush();

            $this->eventDispatcher->dispatch(new UserEvent($user, $oldCompanyEmail), UserEvent::EDITED);

            return $this->redirect($this->generateUrl('specific_profile', ['id' => $user->getId()]));
        }

        return $this->render('profile/edit_profile.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/kontrollpanel/bruker/vekorepost/endre/{id}', name: 'edit_user_company_email', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function editCompanyEmailAction(Request $request, User $user)
    {
        $oldCompanyEmail = $user->getCompanyEmail();
        $form = $this->createForm(UserCompanyEmailType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->eventDispatcher->dispatch(new UserEvent($user, $oldCompanyEmail), UserEvent::COMPANY_EMAIL_EDITED);

            return $this->redirectToRoute('specific_profile', ['id' => $user->getId()]);
        }

        return $this->render('profile/edit_company_email.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
