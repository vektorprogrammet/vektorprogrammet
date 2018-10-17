<?php


namespace AppBundle\Service;

use AppBundle\Entity\AccessRule;
use AppBundle\Entity\UnhandledAccessRule;
use AppBundle\Entity\User;
use AppBundle\Role\Roles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

class AccessControlService
{
    private $entityManager;
    private $router;
    private $roleManager;
    private $userService;

    /**
     * ResourceAccessSubscriber constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface $router
     * @param RoleManager $roleManager
     * @param UserService $userService
     */
    public function __construct(EntityManagerInterface $entityManager, RouterInterface $router, RoleManager $roleManager, UserService $userService)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->roleManager = $roleManager;
        $this->userService = $userService;
    }

    public function createRule(AccessRule $accessRule)
    {
        $em = $this->entityManager;
        $unhandledRules = $em->getRepository('AppBundle:UnhandledAccessRule')->findByResource($accessRule->getResource());
        foreach ($unhandledRules as $unhandledRule) {
            $em->remove($unhandledRule);
        }

        $em->persist($accessRule);
        $em->flush();
    }

    public function checkAccess($resources, User $user = null) : bool
    {
        $this->markRulesAsUnhandledIfNotExists($resources);

        return $this->checkAccessWithoutMarkingUnhandled($resources, $user);
    }

    public function checkAccessWithoutMarkingUnhandled($resources, User $user = null) : bool
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

    /**
     * @param User|null $user
     * @param string $resource
     * @param string $method
     *
     * @return bool
     */
    private function checkAccessToResourceAndMethod(?User $user, string $resource, string $method = 'GET'): bool
    {
        $accessRules = $this->entityManager->getRepository("AppBundle:AccessRule")->findOneByResourceAndMethod($resource, $method);

        $everyoneHasAccess = !empty(array_filter($accessRules, function (AccessRule $rule) {
            return $rule->isEmpty();
        }));
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
        if (count($rule->getUsers()) > 0 && !($user->isActive() && $this->userIsInRuleUserList($user, $rule))) {
            return false;
        }

        if (count($rule->getTeams()) > 0 && !$this->userHasTeamAccessToRule($user, $rule)) {
            return false;
        }

        if ($rule->isForExecutiveBoard() && empty($user->getActiveExecutiveBoardMemberships())) {
            return false;
        }

        if (count($rule->getRoles()) > 0 && !$this->userRoleHasAccessToRule($user, $rule)) {
            return false;
        }

        return true;
    }

    private function userHasTeamAccessToRule(User $user, AccessRule $rule) : bool
    {
        foreach ($user->getActiveTeamMemberships() as $membership) {
            foreach ($rule->getTeams() as $team) {
                if ($membership->getTeam() === $team) {
                    return true;
                }
            }
        }

        return false;
    }

    private function userIsInRuleUserList(User $user, AccessRule $rule) : bool
    {
        foreach ($rule->getUsers() as $userInRule) {
            if ($user === $userInRule) {
                return true;
            }
        }

        return false;
    }

    private function userRoleHasAccessToRule(User $user, AccessRule $rule) : bool
    {
        foreach ($rule->getRoles() as $roleInRule) {
            foreach ($user->getRoles() as $userRole) {
                if ($roleInRule === $userRole) {
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
            return strlen($resource) > 0 && $resource[0] !== "_";
        }, ARRAY_FILTER_USE_BOTH);

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
        return substr($resource->getPath(), 0, 14) === "/kontrollpanel";
    }

    private function markRulesAsUnhandledIfNotExists($resources)
    {
        if (is_string($resources)) {
            $resource = $resources;
            $this->markRuleAsUnhandledIfNotExists($resource);
            return;
        }

        if (!is_array($resources)) {
            throw new \InvalidArgumentException();
        }

        foreach ($resources as $resource => $method) {
            $onlyRouteSpecified = is_numeric($resource);
            if ($onlyRouteSpecified) {
                $resource = $method;
                $this->markRuleAsUnhandledIfNotExists($resource);
            } else {
                $this->markRuleAsUnhandledIfNotExists($resource);
            }
        }
    }

    private function markRuleAsUnhandledIfNotExists(string $resource)
    {
        if ($this->ruleExists($resource) || $this->unhandledRuleExists($resource)) {
            return;
        }

        $rule = new UnhandledAccessRule($resource);
        $this->entityManager->persist($rule);
        $this->entityManager->flush();
    }

    private function ruleExists(string $resource)
    {
        return !empty($this->entityManager->getRepository('AppBundle:AccessRule')->findByResource($resource));
    }

    private function unhandledRuleExists(string $resource)
    {
        return !empty($this->entityManager->getRepository('AppBundle:UnhandledAccessRule')->findByResource($resource));
    }
}
