<?php


namespace AppBundle\Service;

use AppBundle\Entity\AccessRule;
use AppBundle\Entity\User;
use AppBundle\Role\Roles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AccessControlService
{
    private $entityManager;
    private $router;
    /**
     * @var RoleManager
     */
    private $roleManager;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * ResourceAccessSubscriber constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface $router
     * @param RoleManager $roleManager
     * @param TokenStorageInterface $tokenStorage
     * @internal param Roles $roleService
     */
    public function __construct(EntityManagerInterface $entityManager, RouterInterface $router, RoleManager $roleManager, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->roleManager = $roleManager;
        $this->tokenStorage = $tokenStorage;
    }

    public function checkAccess($resources, User $user = null) : bool
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
    public function checkAccessToResourceAndMethod(?User $user, string $resource, string $method = 'GET'): bool
    {
        $accessRules = $this->entityManager->getRepository("AppBundle:AccessRule")->findOneByResourceAndMethod($resource, $method);

        $everyoneHasAccess = empty($accessRules);
        if ($everyoneHasAccess) {
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
        $token = $this->tokenStorage->getToken();
        /**
         * @var User|null $user
         */
        $user = $token ? $token->getUser() : null;
        if (is_string($user)) {
            $user = null;
        }

        return $user;
    }

    private function userHasAccessToRule(User $user, AccessRule $rule): bool
    {
        $userHasAccess = $this->entityManager->getRepository("AppBundle:AccessRule")->userIsInAccessRule($user, $rule);
        if (count($rule->getUsers()) > 0 && !$userHasAccess) {
            return false;
        }

        $teamHasAccess = $this->entityManager->getRepository("AppBundle:AccessRule")->usersTeamIsInAccessRule($user, $rule);
        if (count($rule->getTeams()) > 0 && !$teamHasAccess) {
            return false;
        }

        $roleHasAccess = $this->entityManager->getRepository("AppBundle:AccessRule")->roleIsInAccessRule($user->getRoles()[0], $rule);
        if (count($rule->getRoles()) > 0 && !$roleHasAccess) {
            return false;
        }

        return true;
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
}
