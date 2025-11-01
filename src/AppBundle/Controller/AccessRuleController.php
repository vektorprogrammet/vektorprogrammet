<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AccessRule;
use AppBundle\Entity\UnhandledAccessRule;
use AppBundle\Form\Type\AccessRuleType;
use AppBundle\Form\Type\RoutingAccessRuleType;
use AppBundle\Role\ReversedRoleHierarchy;
use AppBundle\Role\Roles;
use AppBundle\Service\Contract\AccessControlServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AccessRuleController extends BaseController
{
    private $entityManager;
    private $reversedRoleHierarchy;
    private $accessControlService;

    /**
     * @param EntityManagerInterface $entityManager
     * @param ReversedRoleHierarchy $reversedRoleHierarchy
     * @param AccessControlServiceInterface $accessControlService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ReversedRoleHierarchy $reversedRoleHierarchy,
        AccessControlServiceInterface $accessControlService
    ) {
        $this->entityManager = $entityManager;
        $this->reversedRoleHierarchy = $reversedRoleHierarchy;
        $this->accessControlService = $accessControlService;
    }
    
    /**
     * @Route("/kontrollpanel/admin/accessrules", name="access_rules_show")
     * @return Response
     */
    public function indexAction()
    {
        $customRules = $this->entityManager->getRepository(AccessRule::class)->findCustomRules();
        $routingRules = $this->entityManager->getRepository(AccessRule::class)->findRoutingRules();
        $unhandledRules = $this->entityManager->getRepository(UnhandledAccessRule::class)->findAll();
        return $this->render('admin/access_rule/index.html.twig', array(
            'customRules' => $customRules,
            'routingRules' => $routingRules,
            'unhandledRules' => $unhandledRules
        ));
    }

    /**
     * @Route("/kontrollpanel/admin/accessrules/edit/{id}",
     *     name="access_rules_edit",
     *     requirements={"id"="\d+"}
     * )
     *
     * @Route("/kontrollpanel/admin/accessrules/create",
     *     name="access_rules_create",
     *     defaults={"id": null},
     *     requirements={"id"="\d+"}
     * )
     * @param Request $request
     * @param AccessRule|null $accessRule
     * @return Response
     */
    public function createRuleAction(Request $request, AccessRule $accessRule = null)
    {
        if ($isCreate = $accessRule === null) {
            $accessRule = new AccessRule();
        }
        $roles = $this->reversedRoleHierarchy->getParentRoles([ Roles::TEAM_MEMBER ]);
        $form = $this->createForm(AccessRuleType::class, $accessRule, [
            'roles' => $roles
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $this->accessControlService->createRule($accessRule);

            if ($isCreate) {
                $this->addFlash("success", "Access rule created");
            } else {
                $this->addFlash("success", "Access rule edited");
            }
            
            return $this->redirectToRoute("access_rules_show");
        }
        return $this->render('admin/access_rule/create.html.twig', array(
            'form' => $form->createView(),
            'accessRule' => $accessRule,
            'isCreate' => $isCreate
        ));
    }

    /**
     * @Route("/kontrollpanel/admin/accessrules/routing/edit/{id}",
     *     name="access_rules_edit_routing",
     *     requirements={"id"="\d+"}
     * )
     *
     * @Route("/kontrollpanel/admin/accessrules/routing/create",
     *     name="access_rules_create_routing",
     *     defaults={"id": null},
     *     requirements={"id"="\d+"}
     * )
     * @param Request $request
     * @param AccessRule|null $accessRule
     * @return Response
     */
    public function createRoutingRuleAction(Request $request, AccessRule $accessRule = null)
    {
        if ($isCreate = $accessRule === null) {
            $accessRule = new AccessRule();
        }
        $roles = $this->reversedRoleHierarchy->getParentRoles([ Roles::TEAM_MEMBER ]);
        $routes = $this->accessControlService->getRoutes();
        $form = $this->createForm(RoutingAccessRuleType::class, $accessRule, [
            'routes' => $routes,
            'roles' => $roles
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $accessRule->setIsRoutingRule(true);
            $this->accessControlService->createRule($accessRule);

            if ($isCreate) {
                $this->addFlash("success", "Access rule created");
            } else {
                $this->addFlash("success", "Access rule edited");
            }

            return $this->redirectToRoute("access_rules_show");
        }
        return $this->render('admin/access_rule/create.html.twig', array(
            'form' => $form->createView(),
            'accessRule' => $accessRule,
            'isCreate' => $isCreate
        ));
    }

    /**
     * @Route("/kontrollpanel/admin/accessrules/copy/{id}",
     *     name="access_rules_copy",
     *     requirements={"id"="\d+"}
     * )
     *
     * @param Request $request
     * @param AccessRule $rule
     * @return Response
     */
    public function copyAccessRuleAction(Request $request, AccessRule $rule)
    {
        $clone = clone $rule;
        if ($rule->isRoutingRule()) {
            return $this->createRoutingRuleAction($request, $clone);
        }
        
        return $this->createRuleAction($request, $clone);
    }

    /**
     * @Route("/kontrollpanel/admin/accessrules/delete/{id}",
     *     name="access_rules_delete",
     *     requirements={"id"="\d+"},
     *     methods={"POST"}
     * )
     * @param AccessRule $accessRule
     * @return Response
     */
    public function deleteAction(AccessRule $accessRule)
    {
        $this->entityManager->remove($accessRule);
        $this->entityManager->flush();

        $this->addFlash("success", $accessRule->getName()." removed");

        return $this->redirectToRoute("access_rules_show");
    }
}
