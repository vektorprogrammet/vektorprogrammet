<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use AppBundle\Entity\User;
use AppBundle\Form\Type\CreateUserType;
use AppBundle\Entity\Role;
use AppBundle\Repository\Contract\DepartmentRepositoryInterface;
use AppBundle\Repository\Contract\UserRepositoryInterface;
use AppBundle\Role\Roles;
use AppBundle\Service\Contract\UserManagementServiceInterface;
use AppBundle\Service\Contract\UserRegistrationInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class UserAdminController extends BaseController
{
    private $departmentRepository;
    private $userRepository;
    private $entityManager;
    private $userRegistration;
    private $userManagementService;

    /**
     * @param DepartmentRepositoryInterface $departmentRepository
     * @param UserRepositoryInterface $userRepository
     * @param EntityManagerInterface $entityManager
     * @param UserRegistrationInterface $userRegistration
     * @param UserManagementServiceInterface $userManagementService
     */
    public function __construct(
        DepartmentRepositoryInterface $departmentRepository,
        UserRepositoryInterface $userRepository,
        EntityManagerInterface $entityManager,
        UserRegistrationInterface $userRegistration,
        UserManagementServiceInterface $userManagementService
    ) {
        $this->departmentRepository = $departmentRepository;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->userRegistration = $userRegistration;
        $this->userManagementService = $userManagementService;
    }
    public function createUserAction(Request $request, ?Department $department = null)
    {
        if (!$this->isGranted(Roles::TEAM_LEADER) || $department === null) {
            $department = $this->getUser()->getDepartment();
        }

        // Create the user object
        $user = new User();

        $form = $this->createForm(CreateUserType::class, $user, array(
            'validation_groups' => array('create_user'),
            'department' => $department
        ));

        // Handle the form
        $form->handleRequest($request);

        // The fields of the form is checked if they contain the correct information
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userManagementService->createUserWithDefaults($user, $department);

            return $this->redirectToRoute('useradmin_show');
        }

        // Render the view
        return $this->render('user_admin/create_user.html.twig', array(
            'form' => $form->createView(),
            'department' => $department,
        ));
    }

    public function showAction()
    {
        // Finds the department for the current logged in user
        $department = $this->getUser()->getDepartment();

        $userData = $this->userManagementService->getFilteredUsersByDepartment($department);

        return $this->render('user_admin/index.html.twig', array(
            'activeUsers' => $userData['activeUsers'],
            'inActiveUsers' => $userData['inActiveUsers'],
            'departments' => $userData['activeDepartments'],
            'department' => $department,
        ));
    }

    public function showUsersByDepartmentAction(Department $department)
    {
        $userData = $this->userManagementService->getFilteredUsersByDepartment($department);

        // Renders the view with the variables
        return $this->render('user_admin/index.html.twig', array(
            'activeUsers' => $userData['activeUsers'],
            'inActiveUsers' => $userData['inActiveUsers'],
            'departments' => $userData['activeDepartments'],
            'department' => $department,
        ));
    }

    public function deleteUserByIdAction(User $user)
    {
        if ($user === $this->getUser()) {
            $this->addFlash("error", "Du kan ikke slette deg selv.");
        } elseif ($this->isGranted(ROLES::ADMIN) || $user->getDepartment() == $this->getUser()->getDepartment()) {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
            $this->addFlash("success", "$user har blitt slettet.");
        } else {
            throw $this->createAccessDeniedException();
        }
        // Redirect to useradmin page, set department to that of the deleted user
        return $this->redirectToRoute('useradmin_filter_users_by_department', array('id' => $user->getDepartment()->getId()));
    }

    public function sendActivationMailAction(User $user)
    {
        $this->userRegistration->sendActivationCode($user);

        return $this->redirectToRoute('useradmin_show');
    }
}
