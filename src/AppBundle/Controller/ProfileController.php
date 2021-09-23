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
use AppBundle\Service\LogService;
use AppBundle\Service\RoleManager;
use AppBundle\Service\UserRegistration;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class ProfileController extends BaseController
{
    public function showAction()
    {
        // Get the user currently signed in
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        // Fetch the assistant history of the user
        $assistantHistory = $em->getRepository(AssistantHistory::class)->findByUser($user);

        // Find the team history of the user
        $teamMemberships = $em->getRepository(TeamMembership::class)->findByUser($user);

        // Find the executive board history of the user
        $executiveBoardMemberships = $em->getRepository(ExecutiveBoardMembership::class)->findByUser($user);

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

        $em = $this->getDoctrine()->getManager();

        // Find the work history of the user
        $teamMemberships = $em->getRepository(TeamMembership::class)->findByUser($user);

        // Find the executive board history of the user
        $executiveBoardMemberships = $em->getRepository(ExecutiveBoardMembership::class)->findByUser($user);

        $isGrantedAssistant = ($this->getUser() !== null && $this->get(RoleManager::class)->userIsGranted($this->getUser(), Roles::ASSISTANT));

        if (empty($teamMemberships) && empty($executiveBoardMemberships) && !$isGrantedAssistant) {
            throw $this->createAccessDeniedException();
        }

        // Fetch the assistant history of the user
        $assistantHistory = $em->getRepository(AssistantHistory::class)->findByUser($user);

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

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->redirectToRoute('specific_profile', ['id' => $user->getId()]);
    }

    public function activateUserAction(User $user)
    {
        $user->setActive(true);

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->redirectToRoute('specific_profile', ['id' => $user->getId()]);
    }

    public function activateNewUserAction(Request $request, $newUserCode)
    {
        $user = $this->get(UserRegistration::class)->activateUserByNewUserCode($newUserCode);

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
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $token = new UsernamePasswordToken($user, null, 'secured_area', $user->getRoles());
            $this->get('security.token_storage')->setToken($token);
            $this->get('session')->set('_security_secured_area', serialize($token));

            $this->get(LogService::class)->info("User $user activated with new user code");

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

        $roleManager = $this->get(RoleManager::class);
        $roleName    = $roleManager->mapAliasToRole($request->request->get('role'));

        if (! $roleManager->loggedInUserCanChangeRoleOfUsersWithRole($user, $roleName)) {
            throw new BadRequestHttpException();
        }

        try {
            $role = $this->getDoctrine()->getRepository(Role::class)->findByRoleName($roleName);
            $user->setRoles(array( $role ));

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

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
        $em = $this->getDoctrine()->getManager();
        // Fetch the assistant history of the user
        $assistantHistory = $em->getRepository(AssistantHistory::class)->findByUser($user);
        // Find the work history of the user
        $teamMembership = $em->getRepository(TeamMembership::class)->findByUser($user);
        // Find the signature of the user creating the certificate
        $signature = $this->getDoctrine()->getRepository(Signature::class)->findByUser($this->getUser());
        // Find department
        $department = $this->getUser()->getDepartment();
        // Find any additional comment
        $additional_comment = $signature->getAdditionalComment();

        if ($signature === null) {
            return $this->redirectToRoute('certificate_show');
        }

        ##########

        # For debugging images
        //return $this->render('certificate/certificate.html.twig', array(
        $html = $this->renderView('certificate/certificate.html.twig', array(
            'user'                  => $user,
            'assistantHistory'      => $assistantHistory,
            'teamMembership'        => $teamMembership,
            'signature'             => $signature,
            'additional_comment'    => $additional_comment,
            'department'            => $department,
            'base_dir'              => $this->get('kernel')->getRootDir() . '/../web' . $request->getBasePath(),
        ));

        ###############


        $options = new Options();
        $options->setIsRemoteEnabled(true);
        //$options->isHtml5ParserEnabled(true); // method call is provided 1 parameters, but the method signature uses 0 parameters

        $dompdf = new Dompdf($options);
        $dompdf->setPaper('A4');

        $html = preg_replace('/>\s+</', "><", $html);
        $dompdf->loadHtml($html);


        # Needs fix
        $base_dir = $this->get('kernel')->getRootDir() . '/../web' . $request->getBasePath();
        $dompdf->setBasePath($base_dir);
        ## ----- ##

        $dompdf->render();
        $dompdf->stream(
            $filename='attest.pdf'
        );

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
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->get('event_dispatcher')->dispatch(UserEvent::EDITED, new UserEvent($user, $oldCompanyEmail));

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
        $form            = $this->createForm(EditUserType::class, $user, array(
            'department' => $user->getDepartment(),
        ));
        $oldCompanyEmail = $user->getCompanyEmail();

        // Handle the form
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->get('event_dispatcher')->dispatch(UserEvent::EDITED, new UserEvent($user, $oldCompanyEmail));

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
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->get('event_dispatcher')->dispatch(UserEvent::COMPANY_EMAIL_EDITED, new UserEvent($user, $oldCompanyEmail));

            return $this->redirectToRoute('specific_profile', [ 'id' => $user->getId() ]);
        }

        return $this->render('profile/edit_company_email.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
