<?php


namespace App\Service;

use App\Models\AccessRule;
use App\Models\UnhandledAccessRule;
use App\Models\User;
use App\Repository\Contract\AccessRuleRepositoryInterface;
use App\Repository\Contract\UnhandledAccessRuleRepositoryInterface;
use App\Role\Roles;
use InvalidArgumentException;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;
use App\Service\Contract\AccessControlServiceInterface;
use App\Service\Contract\RoleManagerInterface;
use App\Service\Contract\UserServiceInterface;

class AccessControlService implements AccessControlServiceInterface
{
    private AccessRuleRepositoryInterface $accessRuleRepository;
    private UnhandledAccessRuleRepositoryInterface $unhandledAccessRuleRepository;
    private RouterInterface $router;
    private RoleManagerInterface $roleManager;
    private UserServiceInterface $userService;
    private array $accessRulesCache;
    private array $unhandledRulesCache;

    /**
     * ResourceAccessSubscriber constructor.
     *
     * @param AccessRuleRepositoryInterface $accessRuleRepository
     * @param UnhandledAccessRuleRepositoryInterface $unhandledAccessRuleRepository
     * @param RouterInterface $router
     * @param RoleManagerInterface $roleManager
     * @param UserServiceInterface $userService
     */
    public function __construct(
        AccessRuleRepositoryInterface $accessRuleRepository,
        UnhandledAccessRuleRepositoryInterface $unhandledAccessRuleRepository,
        RouterInterface $router,
        RoleManagerInterface $roleManager,
        UserServiceInterface $userService
    ) {
        $this->accessRuleRepository = $accessRuleRepository;
        $this->unhandledAccessRuleRepository = $unhandledAccessRuleRepository;
        $this->router = $router;
        $this->roleManager = $roleManager;
        $this->userService = $userService;
        $this->accessRulesCache = [];
        $this->unhandledRulesCache = [];
        $this->preloadCache();
    }

    private function preloadCache(): void
    {
        $accessRules = $this->accessRuleRepository->findAll();
        foreach ($accessRules as $rule) {
            $key = $this->getKey($rule->resource ?? '', $rule->method ?? 'GET');
            if (!key_exists($key, $this->accessRulesCache)) {
                $this->accessRulesCache[$key] = [];
            }
            $this->accessRulesCache[$key][] = $rule;
        }

        $unhandledRules = $this->unhandledAccessRuleRepository->findAll();
        foreach ($unhandledRules as $rule) {
            $key = $this->getKey($rule->resource ?? '', $rule->method ?? 'GET');
            if (!key_exists($key, $this->unhandledRulesCache)) {
                $this->unhandledRulesCache[$key] = [];
            }
            $this->unhandledRulesCache[$key][] = $rule;
        }
    }

    public function createRule(AccessRule $accessRule): void
    {
        $unhandledRules = $this->unhandledAccessRuleRepository->findByResourceAndMethod(
            $accessRule->resource ?? '',
            $accessRule->method ?? 'GET'
        );
        foreach ($unhandledRules as $unhandledRule) {
            $unhandledRule->delete();
        }

        $accessRule->save();

        $this->preloadCache();
    }

    public function checkAccess($resources, User $user = null): bool
    {
        if ($user === null) {
            $user = $this->getLoggedInUser();
        }

        if (is_string($resources)) {
            $resource = $resources;

            return $this->checkAccessToResourceAndMethod($user, $resource);
        }

        if (! is_array($resources)) {
            throw new InvalidArgumentException();
        }

        foreach ($resources as $resource => $method) {
            $onlyRouteSpecified = is_numeric($resource);
            if ($onlyRouteSpecified) {
                $resource  = $method;
                $hasAccess = $this->checkAccessToResourceAndMethod($user, $resource);
            } else {
                $hasAccess = $this->checkAccessToResourceAndMethod($user, $resource, $method);
            }

            if (! $hasAccess) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param User|null $user
     * @param string $resource
     * @param string $method
     *
     * @return bool
     */
    private function checkAccessToResourceAndMethod(?User $user, string $resource, string $method = 'GET'): bool
    {
        $accessRules = $this->getAccessRules($resource, $method);

        if (empty($accessRules)) {
            $this->markRuleAsUnhandledIfNotExists($resource, $method);
        }

        $everyoneHasAccess = ! empty(array_filter($accessRules, function (AccessRule $rule) {
            // Note: isEmpty() method needs to exist on AccessRule model
            return method_exists($rule, 'isEmpty') && $rule->isEmpty();
        }));
        if (empty($accessRules) || $everyoneHasAccess) {
            return true;
        }

        $userRoles = $user->roles;
        if ($user === null || ($userRoles === null || $userRoles->isEmpty())) {
            return false;
        }

        if ($this->roleManager->userIsGranted($user, Roles::ADMIN)) {
            return true;
        }

        foreach ($accessRules as $accessRule) {
            if ($this->userHasAccessToRule($user, $accessRule)) {
                return true;
            }
        }

        return false;
    }

    private function getLoggedInUser()
    {
        return $this->userService->getCurrentUser();
    }

    private function userHasAccessToRule(User $user, AccessRule $rule): bool
    {
        $ruleUsers = $rule->users ?? collect([]);
        if ($ruleUsers->isNotEmpty() && !($user->is_active && $this->userIsInRuleUserList($user, $rule))) {
            return false;
        }

        if (!$this->userHasTeamOrExecutiveBoardAccessToRule($user, $rule)) {
            return false;
        }

        $ruleRoles = $rule->roles ?? collect([]);
        if ($ruleRoles->isNotEmpty() && !$this->userRoleHasAccessToRule($user, $rule)) {
            return false;
        }

        return true;
    }

    private function userHasTeamOrExecutiveBoardAccessToRule(User $user, AccessRule $rule): bool
    {
        $ruleTeams = $rule->teams ?? collect([]);
        $teamRule = $ruleTeams->isNotEmpty();
        // Note: isForExecutiveBoard() method needs to exist on AccessRule model
        $executiveRule = method_exists($rule, 'isForExecutiveBoard') && $rule->isForExecutiveBoard();
        $hasTeamAccess = $this->userHasTeamAccessToRule($user, $rule);
        // Note: getActiveExecutiveBoardMemberships() method needs to exist on User model
        $executiveBoardMemberships = method_exists($user, 'getActiveExecutiveBoardMemberships') 
            ? $user->getActiveExecutiveBoardMemberships() 
            : collect([]);
        $hasExecutiveBoardAccess = $executiveBoardMemberships->isNotEmpty();
        
        if ($teamRule && $executiveRule && !($hasTeamAccess || $hasExecutiveBoardAccess)) {
            return false;
        } elseif ($teamRule && !$executiveRule && !$hasTeamAccess) {
            return false;
        } elseif ($executiveRule && !$teamRule && !$hasExecutiveBoardAccess) {
            return false;
        }

        return true;
    }

    private function userHasTeamAccessToRule(User $user, AccessRule $rule): bool
    {
        $ruleTeams = $rule->teams ?? collect([]);
        if ($ruleTeams->isEmpty()) {
            return false;
        }

        // Note: getActiveTeamMemberships() method needs to exist on User model
        $teamMemberships = method_exists($user, 'getActiveTeamMemberships') 
            ? $user->getActiveTeamMemberships() 
            : collect([]);

        foreach ($teamMemberships as $membership) {
            $team = $membership->team ?? null;
            foreach ($ruleTeams as $ruleTeam) {
                if ($team && $team->id === $ruleTeam->id) {
                    return true;
                }
            }
        }

        return false;
    }

    private function userIsInRuleUserList(User $user, AccessRule $rule): bool
    {
        $ruleUsers = $rule->users ?? collect([]);
        foreach ($ruleUsers as $userInRule) {
            if ($user->id === $userInRule->id) {
                return true;
            }
        }

        return false;
    }

    private function userRoleHasAccessToRule(User $user, AccessRule $rule): bool
    {
        $ruleRoles = $rule->roles ?? collect([]);
        $userRoles = $user->roles ?? collect([]);
        
        foreach ($ruleRoles as $roleInRule) {
            foreach ($userRoles as $userRole) {
                if ($roleInRule->id === $userRole->id) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getRoutes(): array
    {
        $resources = $this->router->getRouteCollection()->all();
        $resources = array_filter($resources, function ($v, string $resource) {
            return strlen($resource) > 0 && !$this->isPrivateRoute($resource);
        }, ARRAY_FILTER_USE_BOTH);

        uasort($resources, function (Route $a, Route $b) {
            if ($this->isControlPanelRoute($a) && ! $this->isControlPanelRoute($b)) {
                return - 1;
            }
            if ($this->isControlPanelRoute($b) && ! $this->isControlPanelRoute($a)) {
                return 1;
            }

            return strcmp($a->getPath(), $b->getPath());
        });

        return $resources;
    }

    private function isControlPanelRoute(Route $resource)
    {
        return substr($resource->getPath(), 0, 14) === "/kontrollpanel";
    }

    private function isPrivateRoute(string $route): bool
    {
        return
            strlen($route) > 0 &&
            substr($route, 0, 1) === '_' &&
            $this->isRoute($route);
    }

    public function getPath(string $name): string
    {
        if (! $this->isRoute($name)) {
            return $name;
        }

        return $this->router->getRouteCollection()->get($name)->getPath();
    }

    private function isRoute(string $name)
    {
        return $this->router->getRouteCollection()->get($name) !== null;
    }

    private function markRuleAsUnhandledIfNotExists(string $resource, string $method = 'GET'): void
    {
        if ($this->isPrivateRoute($resource) || $this->unhandledRuleExists($resource, $method)) {
            return;
        }

        $unhandledRule = new UnhandledAccessRule();
        $unhandledRule->resource = $resource;
        $unhandledRule->method = $method;
        $unhandledRule->save();

        $this->preloadCache();
    }

    private function unhandledRuleExists(string $resource, $method)
    {
        return ! empty($this->getUnhandledRules($resource, $method));
    }

    private function getAccessRules(string $resource, string $method)
    {
        $key = $this->getKey($resource, $method);
        if (key_exists($key, $this->accessRulesCache)) {
            return $this->accessRulesCache[$key];
        }

        return [];
    }

    private function getUnhandledRules(string $resource, string $method)
    {
        $key = $this->getKey($resource, $method);
        if (key_exists($key, $this->unhandledRulesCache)) {
            return $this->unhandledRulesCache[$key];
        }

        return [];
    }

    private function getKey(string $resource, string $method)
    {
        return "$method-$resource";
    }
}
