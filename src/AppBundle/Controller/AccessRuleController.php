<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AccessRule;
use AppBundle\Form\Type\AccessRuleType;
use AppBundle\Form\Type\AccessRuleUserType;
use AppBundle\Form\Type\RoutingAccessRuleType;
use AppBundle\Role\Roles;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AccessRuleController extends Controller
{
    
    /**
     * @Route("/kontrollpanel/admin/accessrules", name="access_rules_show")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $customRules = $this->getDoctrine()->getRepository('AppBundle:AccessRule')->findCustomRules();
        $routingRules = $this->getDoctrine()->getRepository('AppBundle:AccessRule')->findRoutingRules();
        return $this->render('admin/access_rule/index.html.twig', array(
            'customRules' => $customRules,
            'routingRules' => $routingRules,
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
     * @param AccessRule $accessRule
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createRuleAction(Request $request, AccessRule $accessRule = null)
    {
        if ($isCreate = $accessRule === null) {
            $accessRule = new AccessRule();
        }
        $roles = $this->get('app.reversed_role_hierarchy')->getParentRoles([ Roles::TEAM_MEMBER ]);
        $form = $this->createForm(AccessRuleType::class, $accessRule, [
            'roles' => $roles
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($accessRule);
            $em->flush();
            
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
     * @param AccessRule $accessRule
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createRoutingRuleAction(Request $request, AccessRule $accessRule = null)
    {
        if ($isCreate = $accessRule === null) {
            $accessRule = new AccessRule();
        }
        $roles = $this->get('app.reversed_role_hierarchy')->getParentRoles([ Roles::TEAM_MEMBER ]);
        $routes = $this->get('app.access_control')->getRoutes();
        $form = $this->createForm(RoutingAccessRuleType::class, $accessRule, [
            'routes' => $routes,
            'roles' => $roles
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $accessRule->setIsRoutingRule(true);
            $em = $this->getDoctrine()->getManager();
            $em->persist($accessRule);
            $em->flush();

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
     * @Route("/kontrollpanel/admin/accessrules/delete/{id}",
     *     name="access_rules_delete",
     *     requirements={"id"="\d+"}
     * )
     * @Method("POST")
     * @param AccessRule $accessRule
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(AccessRule $accessRule)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($accessRule);
        $em->flush();

        $this->addFlash("success", $accessRule->getName()." removed");

        return $this->redirectToRoute("access_rules_show");
    }
}
