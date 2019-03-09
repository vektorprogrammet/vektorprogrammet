<?php

namespace AppBundle\Controller;

use AppBundle\Entity\UserGroupCollection;
use AppBundle\Form\Type\UserGroupCollectionType;
use AppBundle\Service\UserGroupCollectionManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UserGroupCollectionController extends BaseController
{
    public function createUserGroupCollectionAction(Request $request, UserGroupCollection $userGroupCollection = null)
    {
        if ($isCreate = $userGroupCollection === null) {
            $userGroupCollection = new UserGroupCollection();
        }

        $em = $this->getDoctrine()->getManager();

        $bolkNames = $em
            ->getRepository('AppBundle:AssistantHistory')
            ->findAllBolkNames();


        $form = $this->createForm(UserGroupCollectionType::class, $userGroupCollection, array(
            'bolkNames' => $bolkNames,
            'isCreate' => $isCreate,
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->get(UserGroupCollectionManager::class)->initializeUserGroupCollection($userGroupCollection);
            } catch (\InvalidArgumentException $e) {
                $this->addFlash("danger", $e->getMessage());
                return $this->redirect($this->generateUrl('usergroup_collection_create'));
            } catch (\UnexpectedValueException $e) {
                $this->addFlash("danger", $e->getMessage());
                return $this->redirect($this->generateUrl('usergroup_collection_create'));
            }
            $this->addFlash("success", "Brukergruppering laget");
            // Need some form of redirect. Will cause wrong database entries if the form is rendered again
            // after a valid submit, without remaking the form with up to date question objects from the database.
            return $this->redirect($this->generateUrl('usergroup_collection_create'));
        }

        return $this->render('usergroup_collection/create.html.twig', array(
            'form' => $form->createView(),
            'isCreate' => $isCreate,
            'userGroupCollection' => $userGroupCollection,

        ));
    }
}
