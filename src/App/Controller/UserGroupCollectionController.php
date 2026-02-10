<?php

namespace App\Controller;

use App\Entity\AssistantHistory;
use App\Entity\Repository\AssistantHistoryRepository;
use App\Entity\Repository\DepartmentRepository;
use App\Entity\Repository\SemesterRepository;
use App\Entity\UserGroupCollection;
use App\Form\Type\UserGroupCollectionType;
use App\Service\UserGroupCollectionManager;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use UnexpectedValueException;

class UserGroupCollectionController extends BaseController
{
    public function __construct(
        private EntityManagerInterface $em,
        private AssistantHistoryRepository $assistantHistoryRepo,
        private UserGroupCollectionManager $userGroupCollectionManager,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    public function createUserGroupCollectionAction(Request $request, UserGroupCollection $userGroupCollection = null)
    {
        if ($isCreate = $userGroupCollection === null) {
            $userGroupCollection = new UserGroupCollection();
        }
        $isEditable = !$userGroupCollection->isDeletable();

        $bolkNames = $this->assistantHistoryRepo
            ->findAllBolkNames();


        $form = $this->createForm(UserGroupCollectionType::class, $userGroupCollection, array(
            'bolkNames' => $bolkNames,
            'isEdit' => $isEditable,
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$isCreate) {
                foreach ($userGroupCollection->getUserGroups() as $userGroup) {
                    $this->em->remove($userGroup);
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
        $userGroupCollections = $this->em->getRepository(UserGroupCollection::class)->findAll();

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

        $this->em->remove($userGroupCollection);
        $this->em->flush();
        $response['success'] = true;
        return new JsonResponse($response);
    }
}
