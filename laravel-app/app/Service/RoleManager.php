<?php

namespace App\Service;

use App\Models\ExecutiveBoardMembership;
use App\Models\Role;
use App\Models\Semester;
use App\Models\User;
use App\Google\GoogleUsers;
use App\Repository\Contract\ExecutiveBoardMembershipRepositoryInterface;
use App\Repository\Contract\RoleRepositoryInterface;
use App\Repository\Contract\SemesterRepositoryInterface;
use App\Role\Roles;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use App\Service\Contract\RoleManagerInterface;

class RoleManager implements RoleManagerInterface
{
    private array $roles;
    private array $aliases;
    private AuthorizationCheckerInterface $authorizationChecker;
    private ExecutiveBoardMembershipRepositoryInterface $executiveBoardMembershipRepository;
    private SemesterRepositoryInterface $semesterRepository;
    private RoleRepositoryInterface $roleRepository;
    private LoggerInterface $logger;
    private GoogleUsers $googleUserService;

    /**
     * RoleManager constructor.
     *
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param ExecutiveBoardMembershipRepositoryInterface $executiveBoardMembershipRepository
     * @param SemesterRepositoryInterface $semesterRepository
     * @param RoleRepositoryInterface $roleRepository
     * @param LoggerInterface $logger
     * @param GoogleUsers $googleUserService
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        ExecutiveBoardMembershipRepositoryInterface $executiveBoardMembershipRepository,
        SemesterRepositoryInterface $semesterRepository,
        RoleRepositoryInterface $roleRepository,
        LoggerInterface $logger,
        GoogleUsers $googleUserService
    ) {
        $this->roles = array(
            Roles::ASSISTANT,
            Roles::TEAM_MEMBER,
            Roles::TEAM_LEADER,
            Roles::ADMIN,
        );
        $this->aliases = array(
            Roles::ALIAS_ASSISTANT,
            Roles::ALIAS_TEAM_MEMBER,
            Roles::ALIAS_TEAM_LEADER,
            Roles::ALIAS_ADMIN,
        );
        $this->authorizationChecker = $authorizationChecker;
        $this->executiveBoardMembershipRepository = $executiveBoardMembershipRepository;
        $this->semesterRepository = $semesterRepository;
        $this->roleRepository = $roleRepository;
        $this->logger = $logger;
        $this->googleUserService = $googleUserService;
    }

    public function isValidRole(string $role): bool
    {
        return in_array($role, $this->roles) || in_array($role, $this->aliases);
    }

    public function canChangeToRole(string $role): bool
    {
        return
            $role !== Roles::ADMIN &&
            $role !== Roles::ALIAS_ADMIN &&
            $this->isValidRole($role)
        ;
    }

    public function mapAliasToRole(string $alias): string
    {
        if (in_array($alias, $this->roles)) {
            return $alias;
        }

        if (in_array($alias, $this->aliases)) {
            return $this->roles[array_search($alias, $this->aliases)];
        } else {
            throw new InvalidArgumentException('Invalid alias: '.$alias);
        }
    }

    public function loggedInUserCanCreateUserWithRole(string $role): bool
    {
        if (!$this->isValidRole($role)) {
            return false;
        }

        $role = $this->mapAliasToRole($role);

        // Can't create admins
        // Only team leaders and admins can create users with higher permissions than ASSISTANT
        return
            $role !== Roles::ADMIN &&
            !(!$this->authorizationChecker->isGranted(Roles::TEAM_LEADER) &&
                $role !== Roles::ASSISTANT)
        ;
    }

    public function loggedInUserCanChangeRoleOfUsersWithRole(User $user, string $role): bool
    {
        // Teamleaders can't change the role of admins
        $loggedInAsAdmin = $this->authorizationChecker->isGranted(Roles::ADMIN);
        $tryingToChangeAdmin = $this->userIsGranted($user, Roles::ADMIN);

        return
            ($loggedInAsAdmin || !$tryingToChangeAdmin) &&
            $this->canChangeToRole($role);
    }

    public function userIsGranted(User $user, string $role): bool
    {
        $roles = array(
            Roles::ASSISTANT,
            Roles::TEAM_MEMBER,
            Roles::TEAM_LEADER,
            Roles::ADMIN,
        );

        $userRoles = $user->roles;
        if ($userRoles === null || $userRoles->isEmpty()) {
            return false;
        }

        $userRole = $userRoles->first();
        $userRoleName = $userRole->role ?? null;

        if ($userRoleName === null) {
            return false;
        }

        $userAccessLevel = array_search($userRoleName, $roles);
        $roleAccessLevel = array_search($role, $roles);

        if ($userAccessLevel === false || $roleAccessLevel === false) {
            return false;
        }

        return $userAccessLevel >= $roleAccessLevel;
    }

    /**
     * @param User $user
     *
     * @return bool True if role was updated, false if no role changed
     */
    public function updateUserRole(User $user)
    {
        if ($this->userIsInExecutiveBoard($user) || $this->userIsTeamLeader($user)) {
            $updated = $this->setUserRole($user, Roles::TEAM_LEADER);
        } elseif ($this->userIsTeamMember($user)) {
            $updated = $this->setUserRole($user, Roles::TEAM_MEMBER);
        } else {
            $updated = $this->setUserRole($user, Roles::ASSISTANT);
        }

        if ($updated && $user->company_email) {
            $shouldSuspendGoogleUser = !$this->userIsGranted($user, Roles::TEAM_MEMBER);
            $this->googleUserService->updateUser($user->company_email, $user, $shouldSuspendGoogleUser);
        }

        return $updated;
    }

    public function userIsInExecutiveBoard(User $user): bool
    {
        $executiveBoardMembership = $this->executiveBoardMembershipRepository->findByUser($user);

        return !empty($executiveBoardMembership);
    }

    private function userIsTeamLeader(User $user): bool
    {
        return $this->userIsInATeam($user, true);
    }

    private function userIsTeamMember(User $user): bool
    {
        return $this->userIsInATeam($user, false);
    }

    private function userIsInATeam(User $user, bool $teamLeader): bool
    {
        $semester = $this->semesterRepository->findOrCreateCurrentSemester();
        $teamMemberships = $user->teamMemberships ?? [];

        if ($semester === null) {
            return false;
        }

        foreach ($teamMemberships as $teamMembership) {
            // Note: isActiveInSemester() and isTeamLeader() methods need to exist on TeamMembership model
            if (method_exists($teamMembership, 'isActiveInSemester') && 
                method_exists($teamMembership, 'isTeamLeader')) {
                if ($teamMembership->isActiveInSemester($semester) && $teamMembership->isTeamLeader() === $teamLeader) {
                    return true;
                }
            }
        }

        return false;
    }

    private function setUserRole(User $user, string $roleName): bool
    {
        $isValidRole = $this->isValidRole($roleName);
        if (!$isValidRole) {
            throw new InvalidArgumentException("Invalid role $roleName");
        }
        if ($this->userIsGranted($user, Roles::ADMIN)) {
            return false;
        }

        $role = $this->roleRepository->findByRoleName($roleName);
        $userRoles = $user->roles;
        
        // Check if user already has this role
        $roleNeedsToUpdate = true;
        if ($userRoles && $userRoles->isNotEmpty()) {
            foreach ($userRoles as $userRole) {
                if ($userRole->id === $role->id) {
                    $roleNeedsToUpdate = false;
                    break;
                }
            }
        }

        if ($roleNeedsToUpdate) {
            $user->roles()->sync([$role->id]);
            $user->save();

            $department = $user->fieldOfStudy->department ?? null;
            $departmentName = $department ? $department->short_name : 'Unknown';
            $this->logger->info("Automatic role update ($departmentName): $user has been updated to $roleName");
            return true;
        }

        return false;
    }
}
