<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;
use AppBundle\Google\GoogleUsers;
use AppBundle\Role\Roles;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class RoleManager
{
    private $roles = array();
    private $aliases = array();
    private $authorizationChecker;
    private $em;
    private $logger;
    private $googleUserService;

    /**
     * RoleManager constructor.
     *
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param EntityManagerInterface $em
     * @param LoggerInterface $logger
     * @param GoogleUsers $googleUserService
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker, EntityManagerInterface $em, LoggerInterface $logger, GoogleUsers $googleUserService)
    {
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
        $this->em = $em;
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

        if (empty($user->getRoles())) {
            return false;
        }

        $userRole = $user->getRoles()[0]->getRole();

        $userAccessLevel = array_search($userRole, $roles);
        $roleAccessLevel = array_search($role, $roles);

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

        if ($updated && $user->getCompanyEmail()) {
            $shouldSuspendGoogleUser = !$this->userIsGranted($user, Roles::TEAM_MEMBER);
            $this->googleUserService->updateUser($user->getCompanyEmail(), $user, $shouldSuspendGoogleUser);
        }

        return $updated;
    }

    public function userIsInExecutiveBoard(User $user)
    {
        $executiveBoardMembership = $this->em->getRepository('AppBundle:ExecutiveBoardMembership')->findByUser($user);

        return !empty($executiveBoardMembership);
    }

    private function userIsTeamLeader(User $user)
    {
        return $this->userIsInATeam($user, true);
    }

    private function userIsTeamMember(User $user)
    {
        return $this->userIsInATeam($user, false);
    }

    private function userIsInATeam(User $user, bool $teamLeader)
    {
        $semester = $this->em->getRepository('AppBundle:Semester')->findCurrentSemester();
        $teamMemberships = $user->getTeamMemberships();

        if ($semester === null) {
            return false;
        }

        foreach ($teamMemberships as $teamMembership) {
            if ($teamMembership->isActiveInSemester($semester) && $teamMembership->isTeamLeader() === $teamLeader) {
                return true;
            }
        }

        return false;
    }

    private function setUserRole(User $user, string $role)
    {
        $isValidRole = $this->isValidRole($role);
        if (!$isValidRole) {
            throw new InvalidArgumentException("Invalid role $role");
        }
        if ($this->userIsGranted($user, Roles::ADMIN)) {
            return false;
        }

        $role = $this->em->getRepository('AppBundle:Role')->findByRoleName($role);
        $roleNeedsToUpdate = array_search($role, $user->getRoles()) === false;

        if ($roleNeedsToUpdate) {
            $user->setRoles([$role]);
            $this->em->flush();

            $this->logger->info("Automatic role update ({$user->getDepartment()}): $user has been updated to $role");
            return true;
        }

        return false;
    }
}
