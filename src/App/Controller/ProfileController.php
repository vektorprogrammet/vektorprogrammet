<?php

namespace App\Controller;

use App\Entity\AssistantHistory;
use App\Entity\ExecutiveBoardMembership;
use App\Entity\Role;
use App\Entity\Signature;
use App\Entity\TeamMembership;
use App\Entity\User;
use App\Entity\Repository\AssistantHistoryRepository;
use App\Entity\Repository\DepartmentRepository;
use App\Entity\Repository\ExecutiveBoardMembershipRepository;
use App\Entity\Repository\RoleRepository;
use App\Entity\Repository\SemesterRepository;
use App\Entity\Repository\SignatureRepository;
use App\Entity\Repository\TeamMembershipRepository;
use App\Event\UserEvent;
use App\Form\Type\EditUserPasswordType;
use App\Form\Type\EditUserType;
use App\Form\Type\NewUserType;
use App\Form\Type\UserCompanyEmailType;
use App\Role\Roles;
use App\Service\LogService;
use App\Service\RoleManager;
use App\Service\UserRegistration;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class ProfileController extends BaseController
{
    public function __construct(
        private AssistantHistoryRepository $assistantHistoryRepo,
        private TeamMembershipRepository $teamMembershipRepo,
        private ExecutiveBoardMembershipRepository $executiveBoardMembershipRepo,
        private RoleRepository $roleRepo,
        private SignatureRepository $signatureRepo,
        private UserRegistration $userRegistration,
        private RoleManager $roleManager,
        private LogService $logService,
        private TokenStorageInterface $tokenStorage,
        private RequestStack $requestStack,
        private KernelInterface $kernel,
        private EntityManagerInterface $em,
        private EventDispatcherInterface $eventDispatcher,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

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
        return $this->render('profile/profile.html.twig', array(
            'user'                      => $user,
            'assistantHistory'          => $assistantHistory,
            'teamMemberships'            => $teamMemberships,
            'executiveBoardMemberships'  => $executiveBoardMemberships,
        ));
    }

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
        return $this->render('profile/profile.html.twig', array(
            'user'                      => $user,
            'assistantHistory'          => $assistantHistory,
            'teamMemberships'            => $teamMemberships,
            'executiveBoardMemberships'  => $executiveBoardMemberships,
        ));
    }

    public function deactivateUserAction(User $user)
    {
        $user->setActive(false);

        $this->em->flush();

        return $this->redirectToRoute('specific_profile', ['id' => $user->getId()]);
    }

    public function activateUserAction(User $user)
    {
        $user->setActive(true);

        $this->em->flush();

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
            $this->em->persist($user);
            $this->em->flush();

            $token = new UsernamePasswordToken($user, 'secured_area', $user->getRoles());
            $this->tokenStorage->setToken($token);
            $this->requestStack->getSession()->set('_security_secured_area', serialize($token));

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

        $roleName    = $this->roleManager->mapAliasToRole($request->request->get('role'));

        if (! $this->roleManager->loggedInUserCanChangeRoleOfUsersWithRole($user, $roleName)) {
            throw new BadRequestHttpException();
        }

        try {
            $role = $this->roleRepo->findByRoleName($roleName);
            $user->setRoles(array( $role ));

            $this->em->persist($user);
            $this->em->flush();

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

        $html = $this->renderView('certificate/certificate.html.twig', array(
            'user'                  => $user,
            'assistantHistory'      => $assistantHistory,
            'teamMembership'        => $teamMembership,
            'signature'             => $signature,
            'additional_comment'    => $additional_comment,
            'department'            => $department,
            'base_dir'              => $this->kernel->getProjectDir() . '/web',
        ));
        $options = new Options();
        $options->setIsRemoteEnabled(true);
        $options->setChroot("/../");

        $dompdf = new Dompdf($options);
        $dompdf->setPaper('A4');

        $html = preg_replace('/>\s+</', "><", $html);
        $dompdf->loadHtml($html);

        $dompdf->render();

        $dompdf->stream($filename='attest.pdf');

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
            $this->em->persist($user);
            $this->em->flush();

            $this->eventDispatcher->dispatch(new UserEvent($user, $oldCompanyEmail), UserEvent::EDITED);

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
            $this->em->persist($user);
            $this->em->flush();

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
            $this->em->persist($user);
            $this->em->flush();

            $this->eventDispatcher->dispatch(new UserEvent($user, $oldCompanyEmail), UserEvent::EDITED);

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
            $this->em->flush();

            $this->eventDispatcher->dispatch(new UserEvent($user, $oldCompanyEmail), UserEvent::COMPANY_EMAIL_EDITED);

            return $this->redirectToRoute('specific_profile', [ 'id' => $user->getId() ]);
        }

        return $this->render('profile/edit_company_email.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
