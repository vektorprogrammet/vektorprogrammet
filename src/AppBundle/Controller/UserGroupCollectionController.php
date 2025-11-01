<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AssistantHistory;
use AppBundle\Entity\UserGroupCollection;
use AppBundle\Form\Type\UserGroupCollectionType;
use AppBundle\Repository\Contract\AssistantHistoryRepositoryInterface;
use AppBundle\Service\Contract\UserGroupCollectionManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use UnexpectedValueException;

class UserGroupCollectionController extends BaseController
{
    private $entityManager;
    private $assistantHistoryRepository;
    private $userGroupCollectionManager;

    /**
     * @param EntityManagerInterface $entityManager
     * @param AssistantHistoryRepositoryInterface $assistantHistoryRepository
     * @param UserGroupCollectionManagerInterface $userGroupCollectionManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        AssistantHistoryRepositoryInterface $assistantHistoryRepository,
        UserGroupCollectionManagerInterface $userGroupCollectionManager
    ) {
        $this->entityManager = $entityManager;
        $this->assistantHistoryRepository = $assistantHistoryRepository;
        $this->userGroupCollectionManager = $userGroupCollectionManager;
    }

    public function createUserGroupCollectionAction(Request $request, UserGroupCollection $userGroupCollection = null)
    {
        if ($isCreate = $userGroupCollection === null) {
            $userGroupCollection = new UserGroupCollection();
        }
        $isEditable = !$userGroupCollection->isDeletable();

        $bolkNames = $this->assistantHistoryRepository->findAllBolkNames();


        $form = $this->createForm(UserGroupCollectionType::class, $userGroupCollection, array(
            'bolkNames' => $bolkNames,
            'isEdit' => $isEditable,
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$isCreate) {
                foreach ($userGroupCollection->getUserGroups() as $userGroup) {
                    $this->entityManager->remove($userGroup);
                }
            }

            try {
                $this->userGroupCollectionManager->initializeUserGroupCollection($userGroupCollection);
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
        $userGroupCollections = $this->entityManager->getRepository(UserGroupCollection::class)->findAll();

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

        $this->entityManager->remove($userGroupCollection);
        $this->entityManager->flush();
        $response['success'] = true;
        return new JsonResponse($response);
    }
}
