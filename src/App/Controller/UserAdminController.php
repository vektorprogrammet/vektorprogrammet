<?php

namespace App\Controller;

use App\Support\Controller\BaseController;
use App\Organization\Infrastructure\Entity\Department;
use App\Organization\Infrastructure\Repository\DepartmentRepository;
use App\Entity\Repository\RoleRepository;
use App\Shared\Repository\SemesterRepository;
use App\Entity\Repository\UserRepository;
use App\Entity\User;
use App\Form\Type\CreateUserType;
use App\Role\Roles;
use App\Service\UserRegistration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class UserAdminController extends BaseController
{
    public function __construct(
        private readonly RoleRepository $roleRepo,
        private readonly UserRepository $userRepo,
        private readonly DepartmentRepository $departmentRepo,
        private readonly EntityManagerInterface $em,
        private readonly UserRegistration $userRegistration,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    #[Route('/kontrollpanel/brukeradmin/opprett/{id}', name: 'useradmin_create_user', defaults: ['id' => null], requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function createUserAction(Request $request, ?Department $department = null)
    {
        if (!$this->isGranted(Roles::TEAM_LEADER) || $department === null) {
            $department = $this->getUser()->getDepartment();
        }

        // Create the user object
        $user = new User();

        $form = $this->createForm(CreateUserType::class, $user, [
            'validation_groups' => ['create_user'],
            'department' => $department,
        ]);

        // Handle the form
        $form->handleRequest($request);

        // The fields of the form is checked if they contain the correct information
        if ($form->isSubmitted() && $form->isValid()) {
            $role = $this->roleRepo->findByRoleName(Roles::ASSISTANT);
            $user->addRole($role);

            $this->em->persist($user);
            $this->em->flush();

            $this->userRegistration->sendActivationCode($user);

            return $this->redirectToRoute('useradmin_show');
        }

        // Render the view
        return $this->render('user_admin/create_user.html.twig', [
            'form' => $form->createView(),
            'department' => $department,
        ]);
    }

    #[Route('/kontrollpanel/brukeradmin', name: 'useradmin_show', methods: ['GET'])]
    public function showAction()
    {
        // Finds all the departments
        $activeDepartments = $this->departmentRepo->findActive();

        // Finds the department for the current logged in user
        $department = $this->getUser()->getDepartment();

        $activeUsers = $this->userRepo->findAllActiveUsersByDepartment($department);
        $inActiveUsers = $this->userRepo->findAllInActiveUsersByDepartment($department);

        return $this->render('user_admin/index.html.twig', [
            'activeUsers' => $activeUsers,
            'inActiveUsers' => $inActiveUsers,
            'departments' => $activeDepartments,
            'department' => $department,
        ]);
    }

    #[Route('/kontrollpanel/brukeradmin/avdeling/{id}', name: 'useradmin_filter_users_by_department', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function showUsersByDepartmentAction(Department $department)
    {
        // Finds all the departments
        $activeDepartments = $this->departmentRepo->findActive();

        $activeUsers = $this->userRepo->findAllActiveUsersByDepartment($department);
        $inActiveUsers = $this->userRepo->findAllInActiveUsersByDepartment($department);

        // Renders the view with the variables
        return $this->render('user_admin/index.html.twig', [
            'activeUsers' => $activeUsers,
            'inActiveUsers' => $inActiveUsers,
            'departments' => $activeDepartments,
            'department' => $department,
        ]);
    }

    #[Route('/kontrollpanel/brukeradmin/slett/{id}', name: 'useradmin_delete_user_by_id', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteUserByIdAction(User $user)
    {
        if ($user === $this->getUser()) {
            $this->addFlash('error', 'Du kan ikke slette deg selv.');
        } elseif ($this->isGranted(Roles::ADMIN) || $user->getDepartment() == $this->getUser()->getDepartment()) {
            $this->em->remove($user);
            $this->em->flush();
            $this->addFlash('success', "$user har blitt slettet.");
        } else {
            throw $this->createAccessDeniedException();
        }

        // Redirect to useradmin page, set department to that of the deleted user
        return $this->redirectToRoute('useradmin_filter_users_by_department', ['id' => $user->getDepartment()->getId()]);
    }

    #[Route('/kontrollpanel/brukeradmin/sendaktivering/{id}', name: 'send_user_activation_mail', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function sendActivationMailAction(User $user)
    {
        $this->userRegistration->sendActivationCode($user);

        return $this->redirectToRoute('useradmin_show');
    }
}
