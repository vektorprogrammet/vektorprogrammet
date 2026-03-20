<?php

namespace App\Controller;

use App\Entity\AccessRule;
use App\Entity\Repository\AccessRuleRepository;
use App\Entity\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Entity\Repository\UnhandledAccessRuleRepository;
use App\Form\Type\AccessRuleType;
use App\Form\Type\RoutingAccessRuleType;
use App\Role\ReversedRoleHierarchy;
use App\Role\Roles;
use App\Service\AccessControlService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AccessRuleController extends BaseController
{
    public function __construct(
        private readonly AccessRuleRepository $accessRuleRepo,
        private readonly UnhandledAccessRuleRepository $unhandledAccessRuleRepo,
        private readonly ReversedRoleHierarchy $reversedRoleHierarchy,
        private readonly AccessControlService $accessControlService,
        private readonly EntityManagerInterface $em,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    /**
     * @return Response
     */
    #[Route('/kontrollpanel/admin/accessrules', name: 'access_rules_show')]
    public function indexAction()
    {
        $customRules = $this->accessRuleRepo->findCustomRules();
        $routingRules = $this->accessRuleRepo->findRoutingRules();
        $unhandledRules = $this->unhandledAccessRuleRepo->findAll();

        return $this->render('admin/access_rule/index.html.twig', [
            'customRules' => $customRules,
            'routingRules' => $routingRules,
            'unhandledRules' => $unhandledRules,
        ]);
    }

    /**
     * @return Response
     */
    #[Route('/kontrollpanel/admin/accessrules/edit/{id}', name: 'access_rules_edit', requirements: ['id' => "\d+"])]
    #[Route('/kontrollpanel/admin/accessrules/create', name: 'access_rules_create', defaults: ['id' => null], requirements: ['id' => "\d+"])]
    public function createRuleAction(Request $request, ?AccessRule $accessRule = null)
    {
        if ($isCreate = $accessRule === null) {
            $accessRule = new AccessRule();
        }
        $roles = $this->reversedRoleHierarchy->getParentRoles([Roles::TEAM_MEMBER]);
        $form = $this->createForm(AccessRuleType::class, $accessRule, [
            'roles' => $roles,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->accessControlService->createRule($accessRule);

            if ($isCreate) {
                $this->addFlash('success', 'Access rule created');
            } else {
                $this->addFlash('success', 'Access rule edited');
            }

            return $this->redirectToRoute('access_rules_show');
        }

        return $this->render('admin/access_rule/create.html.twig', [
            'form' => $form->createView(),
            'accessRule' => $accessRule,
            'isCreate' => $isCreate,
        ]);
    }

    /**
     * @return Response
     */
    #[Route('/kontrollpanel/admin/accessrules/routing/edit/{id}', name: 'access_rules_edit_routing', requirements: ['id' => "\d+"])]
    #[Route('/kontrollpanel/admin/accessrules/routing/create', name: 'access_rules_create_routing', defaults: ['id' => null], requirements: ['id' => "\d+"])]
    public function createRoutingRuleAction(Request $request, ?AccessRule $accessRule = null)
    {
        if ($isCreate = $accessRule === null) {
            $accessRule = new AccessRule();
        }
        $roles = $this->reversedRoleHierarchy->getParentRoles([Roles::TEAM_MEMBER]);
        $routes = $this->accessControlService->getRoutes();
        $form = $this->createForm(RoutingAccessRuleType::class, $accessRule, [
            'routes' => $routes,
            'roles' => $roles,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $accessRule->setIsRoutingRule(true);
            $this->accessControlService->createRule($accessRule);

            if ($isCreate) {
                $this->addFlash('success', 'Access rule created');
            } else {
                $this->addFlash('success', 'Access rule edited');
            }

            return $this->redirectToRoute('access_rules_show');
        }

        return $this->render('admin/access_rule/create.html.twig', [
            'form' => $form->createView(),
            'accessRule' => $accessRule,
            'isCreate' => $isCreate,
        ]);
    }

    /**
     * @return Response
     */
    #[Route('/kontrollpanel/admin/accessrules/copy/{id}', name: 'access_rules_copy', requirements: ['id' => "\d+"])]
    public function copyAccessRuleAction(Request $request, AccessRule $rule)
    {
        $clone = clone $rule;
        if ($rule->isRoutingRule()) {
            return $this->createRoutingRuleAction($request, $clone);
        }

        return $this->createRuleAction($request, $clone);
    }

    /**
     * @return Response
     */
    #[Route('/kontrollpanel/admin/accessrules/delete/{id}', name: 'access_rules_delete', requirements: ['id' => "\d+"], methods: ['POST'])]
    public function deleteAction(AccessRule $accessRule)
    {
        $this->em->remove($accessRule);
        $this->em->flush();

        $this->addFlash('success', $accessRule->getName().' removed');

        return $this->redirectToRoute('access_rules_show');
    }
}
