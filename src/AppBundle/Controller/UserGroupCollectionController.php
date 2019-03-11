<?php

namespace AppBundle\Controller;

use AppBundle\Entity\UserGroupCollection;
use AppBundle\Form\Type\UserGroupCollectionType;
use AppBundle\Service\UserGroupCollectionManager;
use Symfony\Component\HttpFoundation\JsonResponse;
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

        $isEdit = $userGroupCollection->activityCounter();

        $em = $this->getDoctrine()->getManager();
        $bolkNames = $em
            ->getRepository('AppBundle:AssistantHistory')
            ->findAllBolkNames();


        $form = $this->createForm(UserGroupCollectionType::class, $userGroupCollection, array(
            'bolkNames' => $bolkNames,
            'isEdit' => $isEdit,
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
            return $this->redirect($this->generateUrl('usergroup_collections'));
        }

        return $this->render('usergroup_collection/usergroup_collection_create.html.twig', array(
            'form' => $form->createView(),
            'isCreate' => $isCreate,
            'userGroupCollection' => $userGroupCollection,

        ));
    }


    /**
     * Deletes the given UserGroupCollection.
     * This method is intended to be called by an Ajax request.
     *
     * @param UserGroupCollection $userGroupCollection
     * @return JsonResponse
     */
    public function deleteUserGroupCollectionAction(UserGroupCollection $userGroupCollection)
    {
        if ($userGroupCollection->activityCounter()){
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($userGroupCollection);
        $em->flush();
        $response['success'] = true;
        return new JsonResponse($response);
    }

    public function userGroupCollectionsAction()
    {
        $userGroupCollections =$this->getDoctrine()->getManager()->getRepository(UserGroupCollection::class)->findAll();

        return $this->render('usergroup_collection/usergroup_collections.html.twig', array(
            'userGroupCollections' => $userGroupCollections,
        ));

    }






}
