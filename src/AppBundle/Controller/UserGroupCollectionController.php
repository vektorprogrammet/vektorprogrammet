<?php

namespace AppBundle\Controller;

use AppBundle\Entity\UserGroupCollection;
use AppBundle\Form\Type\UserGroupCollectionType;
use AppBundle\Service\UserGroupCollectionManager;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use UnexpectedValueException;

class UserGroupCollectionController extends BaseController
{
    public function createUserGroupCollectionAction(Request $request, UserGroupCollection $userGroupCollection = null)
    {
        if ($isCreate = $userGroupCollection === null) {
            $userGroupCollection = new UserGroupCollection();
        }
        $isEditable = !$userGroupCollection->isDeletable();

        $em = $this->getDoctrine()->getManager();
        $bolkNames = $em
            ->getRepository('AppBundle:AssistantHistory')
            ->findAllBolkNames();


        $form = $this->createForm(UserGroupCollectionType::class, $userGroupCollection, array(
            'bolkNames' => $bolkNames,
            'isEdit' => $isEditable,
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$isCreate) {
                foreach ($userGroupCollection->getUserGroups() as $userGroup) {
                    $em->remove($userGroup);
                }
            }

            try {
                $this->get(UserGroupCollectionManager::class)->initializeUserGroupCollection($userGroupCollection);
                $this->addFlash("success", "Brukergruppering laget");
                return $this->redirect($this->generateUrl('usergroup_collections'));
            } catch (InvalidArgumentException $e) {
                $this->addFlash("danger", $e->getMessage());
                return $this->redirect($this->generateUrl('usergroup_collection_create'));
            } catch (UnexpectedValueException $e) {
                $this->addFlash("danger", $e->getMessage());
                return $this->redirect($this->generateUrl('usergroup_collection_create'));
            }
        }

        return $this->render('usergroup_collection/usergroup_collection_create.html.twig', array(
            'form' => $form->createView(),
            'isCreate' => $isCreate,
            'userGroupCollection' => $userGroupCollection,
        ));
    }

    public function userGroupCollectionsAction()
    {
        $userGroupCollections =$this->getDoctrine()->getManager()->getRepository(UserGroupCollection::class)->findAll();

        return $this->render('usergroup_collection/usergroup_collections.html.twig', array(
            'userGroupCollections' => $userGroupCollections,
        ));
    }

    public function deleteUserGroupCollectionAction(UserGroupCollection $userGroupCollection)
    {
        if (!$userGroupCollection->isDeletable()) {
            $response['success'] = false;
            return new JsonResponse($response);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($userGroupCollection);
        $em->flush();
        $response['success'] = true;
        return new JsonResponse($response);
    }
}
