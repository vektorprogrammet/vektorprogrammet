<?php

namespace App\Identity\Infrastructure;

use App\Identity\Infrastructure\Entity\AccessRule;
use App\Identity\Infrastructure\Entity\UnhandledAccessRule;
use App\Identity\Infrastructure\Entity\User;
use App\Identity\Domain\Roles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

class AccessControlService
{
    private $accessRulesCache;
    private $unhandledRulesCache;
    private $cacheLoaded = false;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RouterInterface $router,
        private readonly RoleManager $roleManager,
        private readonly UserService $userService,
    ) {
        $this->accessRulesCache = [];
        $this->unhandledRulesCache = [];
    }

    private function ensureCacheLoaded()
    {
        if ($this->cacheLoaded) {
            return;
        }
        $this->cacheLoaded = true;
        $this->preloadCache();
    }

    private function preloadCache()
    {
        $this->accessRulesCache = [];
        $this->unhandledRulesCache = [];

        $accessRules = $this->entityManager->getRepository(AccessRule::class)->findAll();
        foreach ($accessRules as $rule) {
            $key = $this->getKey($rule->getResource(), $rule->getMethod());
            if (!key_exists($key, $this->accessRulesCache)) {
                $this->accessRulesCache[$key] = [];
            }
            $this->accessRulesCache[$key][] = $rule;
        }

        $unhandledRules = $this->entityManager->getRepository(UnhandledAccessRule::class)->findAll();
        foreach ($unhandledRules as $rule) {
            $key = $this->getKey($rule->getResource(), $rule->getMethod());
            if (!key_exists($key, $this->unhandledRulesCache)) {
                $this->unhandledRulesCache[$key] = [];
            }
            $this->unhandledRulesCache[$key][] = $rule;
        }
    }

    public function createRule(AccessRule $accessRule)
    {
        $em = $this->entityManager;
        $unhandledRules = $em->getRepository(UnhandledAccessRule::class)->findByResourceAndMethod($accessRule->getResource(), $accessRule->getMethod());
        foreach ($unhandledRules as $unhandledRule) {
            $em->remove($unhandledRule);
        }

        $em->persist($accessRule);
        $em->flush();

        $this->cacheLoaded = false;
        $this->ensureCacheLoaded();
    }

    public function checkAccess($resources, ?User $user = null): bool
    {
        if ($user === null) {
            $user = $this->getLoggedInUser();
        }

        if (is_string($resources)) {
            $resource = $resources;

            return $this->checkAccessToResourceAndMethod($user, $resource);
        }

        if (!is_array($resources)) {
            throw new \InvalidArgumentException();
        }

        foreach ($resources as $resource => $method) {
            $onlyRouteSpecified = is_numeric($resource);
            if ($onlyRouteSpecified) {
                $resource = $method;
                $hasAccess = $this->checkAccessToResourceAndMethod($user, $resource);
            } else {
                $hasAccess = $this->checkAccessToResourceAndMethod($user, $resource, $method);
            }

            if (!$hasAccess) {
                return false;
            }
        }

        return true;
    }

    private function checkAccessToResourceAndMethod(?User $user, string $resource, string $method = 'GET'): bool
    {
        $accessRules = $this->getAccessRules($resource, $method);

        if (empty($accessRules)) {
            $this->markRuleAsUnhandledIfNotExists($resource, $method);
        }

        $everyoneHasAccess = !empty(array_filter($accessRules, fn (AccessRule $rule) => $rule->isEmpty()));
        if (empty($accessRules) || $everyoneHasAccess) {
            return true;
        }

        if ($user === null || empty($user->getRoles())) {
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
        if (count(is_countable($rule->getUsers()) ? $rule->getUsers() : []) > 0 && !($user->isActive() && $this->userIsInRuleUserList($user, $rule))) {
            return false;
        }

        if (!$this->userHasTeamOrExecutiveBoardAccessToRule($user, $rule)) {
            return false;
        }

        if (count(is_countable($rule->getRoles()) ? $rule->getRoles() : []) > 0 && !$this->userRoleHasAccessToRule($user, $rule)) {
            return false;
        }

        return true;
    }

    private function userHasTeamOrExecutiveBoardAccessToRule(User $user, AccessRule $rule): bool
    {
        $teamRule = count(is_countable($rule->getTeams()) ? $rule->getTeams() : []) > 0;
        $executiveRule = $rule->isForExecutiveBoard();
        $hasTeamAccess = $this->userHasTeamAccessToRule($user, $rule);
        $hasExecutiveBoardAccess = count(is_countable($user->getActiveExecutiveBoardMemberships()) ? $user->getActiveExecutiveBoardMemberships() : []) > 0;
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
        if (empty($rule->getTeams())) {
            return false;
        }

        foreach ($user->getActiveTeamMemberships() as $membership) {
            foreach ($rule->getTeams() as $team) {
                if ($membership->getTeam() === $team) {
                    return true;
                }
            }
        }

        return false;
    }

    private function userIsInRuleUserList(User $user, AccessRule $rule): bool
    {
        foreach ($rule->getUsers() as $userInRule) {
            if ($user === $userInRule) {
                return true;
            }
        }

        return false;
    }

    private function userRoleHasAccessToRule(User $user, AccessRule $rule): bool
    {
        $userRoleStrings = $user->getRoles();
        foreach ($rule->getRoles() as $roleInRule) {
            $roleString = $roleInRule instanceof \App\Entity\Role ? $roleInRule->getRole() : (string) $roleInRule;
            if (in_array($roleString, $userRoleStrings, true)) {
                return true;
            }
        }

        return false;
    }

    public function getRoutes(): array
    {
        $resources = $this->router->getRouteCollection()->all();
        $resources = array_filter($resources, fn ($v, string $resource) => strlen($resource) > 0 && !$this->isPrivateRoute($resource), ARRAY_FILTER_USE_BOTH);

        uasort($resources, function (Route $a, Route $b) {
            if ($this->isControlPanelRoute($a) && !$this->isControlPanelRoute($b)) {
                return -1;
            }
            if ($this->isControlPanelRoute($b) && !$this->isControlPanelRoute($a)) {
                return 1;
            }

            return strcmp($a->getPath(), $b->getPath());
        });

        return $resources;
    }

    private function isControlPanelRoute(Route $resource)
    {
        return str_starts_with($resource->getPath(), '/kontrollpanel');
    }

    private function isPrivateRoute(string $route): bool
    {
        return
            strlen($route) > 0
            && str_starts_with($route, '_')
            && $this->isRoute($route);
    }

    public function getPath(string $name)
    {
        if (!$this->isRoute($name)) {
            return $name;
        }

        return $this->router->getRouteCollection()->get($name)->getPath();
    }

    private function isRoute(string $name)
    {
        return $this->router->getRouteCollection()->get($name) !== null;
    }

    private function markRuleAsUnhandledIfNotExists(string $resource, string $method = 'GET')
    {
        if ($this->isPrivateRoute($resource) || $this->unhandledRuleExists($resource, $method)) {
            return;
        }

        $this->entityManager->persist(new UnhandledAccessRule($resource, $method));
        $this->entityManager->flush();

        $this->cacheLoaded = false;
        $this->ensureCacheLoaded();
    }

    private function unhandledRuleExists(string $resource, $method)
    {
        return !empty($this->getUnhandledRules($resource, $method));
    }

    private function getAccessRules(string $resource, string $method)
    {
        $this->ensureCacheLoaded();
        $key = $this->getKey($resource, $method);
        if (key_exists($key, $this->accessRulesCache)) {
            return $this->accessRulesCache[$key];
        }

        return [];
    }

    private function getUnhandledRules(string $resource, string $method)
    {
        $this->ensureCacheLoaded();
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
