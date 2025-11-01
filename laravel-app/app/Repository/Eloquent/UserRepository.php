<?php

namespace App\Repository\Eloquent;

use App\Models\Department;
use App\Models\Semester;
use App\Models\User;
use App\Repository\Contract\UserRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

/**
 * Eloquent implementation of UserRepositoryInterface.
 */
class UserRepository implements UserRepositoryInterface
{
    /**
     * Find users in department with team membership in semester.
     *
     * @param Department $department
     * @param Semester $semester
     * @return User[]
     */
    public function findUsersInDepartmentWithTeamMembershipInSemester(
        Department $department,
        Semester $semester
    ): array {
        $users = User::query()
            ->whereHas('teamMemberships.team', function ($query) use ($department) {
                $query->where('department_id', $department->id);
            })
            ->get();

        $teamMembers = [];
        foreach ($users as $user) {
            foreach ($user->teamMemberships as $teamMembership) {
                $startSemester = $teamMembership->startSemester;
                $endSemester = $teamMembership->endSemester;

                if ($semester->isBetween($startSemester, $endSemester)) {
                    $teamMembers[] = $user;
                    continue 2;
                }
            }
        }

        return $teamMembers;
    }

    /**
     * Find users with assistant history in department and semester.
     *
     * @param Department $department
     * @param Semester $semester
     * @return User[]
     */
    public function findUsersWithAssistantHistoryInDepartmentAndSemester(
        Department $department,
        Semester $semester
    ): array {
        return User::query()
            ->whereHas('assistantHistories', function ($query) use ($department, $semester) {
                $query->where('department_id', $department->id)
                      ->where('semester_id', $semester->id);
            })
            ->get()
            ->toArray();
    }

    /**
     * Find all users by department.
     *
     * @param mixed $department Department ID or entity
     * @return User[]
     */
    public function findAllUsersByDepartment($department): array
    {
        $departmentId = $department instanceof Department ? $department->id : $department;

        return User::query()
            ->whereHas('fieldOfStudy.department', function ($query) use ($departmentId) {
                $query->where('id', $departmentId);
            })
            ->get()
            ->toArray();
    }

    /**
     * Find all active users by department.
     *
     * @param mixed $department Department ID or entity
     * @return User[]
     */
    public function findAllActiveUsersByDepartment($department): array
    {
        $departmentId = $department instanceof Department ? $department->id : $department;

        return User::query()
            ->where('is_active', true)
            ->whereHas('fieldOfStudy.department', function ($query) use ($departmentId) {
                $query->where('id', $departmentId);
            })
            ->get()
            ->toArray();
    }

    /**
     * Find all inactive users by department.
     *
     * @param mixed $department Department ID or entity
     * @return User[]
     */
    public function findAllInActiveUsersByDepartment($department): array
    {
        $departmentId = $department instanceof Department ? $department->id : $department;

        return User::query()
            ->where('is_active', false)
            ->whereHas('fieldOfStudy.department', function ($query) use ($departmentId) {
                $query->where('id', $departmentId);
            })
            ->get()
            ->toArray();
    }

    /**
     * Find all users by department and roles.
     *
     * @param mixed $department Department ID or entity
     * @param array $roles Array of role names
     * @return User[]
     */
    public function findAllUsersByDepartmentAndRoles($department, array $roles): array
    {
        $departmentId = $department instanceof Department ? $department->id : $department;

        return User::query()
            ->whereHas('roles', function ($query) use ($roles) {
                $query->whereIn('role', $roles);
            })
            ->whereHas('fieldOfStudy.department', function ($query) use ($departmentId) {
                $query->where('id', $departmentId);
            })
            ->get()
            ->toArray();
    }

    /**
     * Find all users with receipts.
     *
     * @return User[]
     */
    public function findAllUsersWithReceipts(): array
    {
        return User::query()
            ->has('receipts')
            ->get()
            ->toArray();
    }

    /**
     * Find user by username.
     *
     * @param string $username
     * @return User
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findUserByUsername(string $username): User
    {
        return User::where('user_name', $username)->firstOrFail();
    }

    /**
     * Find user by username, email, or company email.
     *
     * @param string $login Username, email, or company email
     * @return User
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findByUsernameOrEmail(string $login): User
    {
        return User::where('user_name', $login)
            ->orWhere('email', $login)
            ->orWhere('company_email', $login)
            ->firstOrFail();
    }

    /**
     * Find user by email.
     *
     * @param string $email
     * @return User|null
     */
    public function findUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Find user by ID.
     *
     * @param int $id
     * @return User
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findUserById(int $id): User
    {
        return User::findOrFail($id);
    }

    /**
     * Find user by new user code.
     *
     * @param string $code
     * @return User|null
     */
    public function findUserByNewUserCode(string $code): ?User
    {
        return User::where('new_user_code', $code)->first();
    }

    /**
     * Find all company emails.
     *
     * @return string[]
     */
    public function findAllCompanyEmails(): array
    {
        return User::query()
            ->whereNotNull('company_email')
            ->pluck('company_email')
            ->toArray();
    }

    /**
     * Find all assistants (users with assistant history).
     *
     * @return User[]
     */
    public function findAssistants(): array
    {
        return User::query()
            ->has('assistantHistories')
            ->distinct()
            ->get()
            ->toArray();
    }

    /**
     * Find all team members (users with team memberships).
     *
     * @return User[]
     */
    public function findTeamMembers(): array
    {
        return User::query()
            ->has('teamMemberships')
            ->distinct()
            ->get()
            ->toArray();
    }

    /**
     * Load user by username (for Symfony UserProviderInterface compatibility).
     *
     * @param string $username
     * @return User
     * @throws \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function loadUserByUsername($username)
    {
        try {
            return $this->findByUsernameOrEmail($username);
        } catch (ModelNotFoundException $e) {
            throw new \Symfony\Component\Security\Core\Exception\UsernameNotFoundException(
                sprintf('Unable to find an active admin VektorVektorBundle:User object identified by "%s".', $username),
                0,
                $e
            );
        }
    }

    /**
     * Refresh user (for Symfony UserProviderInterface compatibility).
     *
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @return User
     * @throws \Symfony\Component\Security\Core\Exception\UnsupportedUserException
     */
    public function refreshUser(\Symfony\Component\Security\Core\User\UserInterface $user)
    {
        if (!$this->supportsClass(get_class($user))) {
            throw new \Symfony\Component\Security\Core\Exception\UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->findUserById($user->getId());
    }

    /**
     * Check if class is supported (for Symfony UserProviderInterface compatibility).
     *
     * @param string $class
     * @return bool
     */
    public function supportsClass($class)
    {
        return $class === User::class || is_subclass_of($class, User::class);
    }
}

