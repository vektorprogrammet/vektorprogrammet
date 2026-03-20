<?php

namespace App\Controller;

use App\Support\Controller\BaseController;
use App\Entity\Repository\AssistantHistoryRepository;
use App\Entity\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Entity\UserGroupCollection;
use App\Form\Type\UserGroupCollectionType;
use App\Service\UserGroupCollectionManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class UserGroupCollectionController extends BaseController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly AssistantHistoryRepository $assistantHistoryRepo,
        private readonly UserGroupCollectionManager $userGroupCollectionManager,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    #[Route('/kontrollpanel/brukergruppesamling/opprett', name: 'usergroup_collection_create', methods: ['GET', 'POST'])]
    #[Route('/kontrollpanel/brukergruppesamling/opprett/{id}', name: 'usergroup_collection_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function createUserGroupCollectionAction(Request $request, ?UserGroupCollection $userGroupCollection = null)
    {
        if ($isCreate = $userGroupCollection === null) {
            $userGroupCollection = new UserGroupCollection();
        }
        $isEditable = !$userGroupCollection->isDeletable();

        $bolkNames = $this->assistantHistoryRepo
            ->findAllBolkNames();

        $form = $this->createForm(UserGroupCollectionType::class, $userGroupCollection, [
            'bolkNames' => $bolkNames,
            'isEdit' => $isEditable,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$isCreate) {
                foreach ($userGroupCollection->getUserGroups() as $userGroup) {
                    $this->em->remove($userGroup);
                }
            }

            try {
                $this->userGroupCollectionManager->initializeUserGroupCollection($userGroupCollection);
                $this->addFlash('success', 'Brukergruppering laget');

                return $this->redirect($this->generateUrl('usergroup_collections'));
            } catch (\InvalidArgumentException|\UnexpectedValueException $e) {
                $this->addFlash('danger', $e->getMessage());

                return $this->redirect($this->generateUrl('usergroup_collection_create'));
            }
        }

        return $this->render('usergroup_collection/usergroup_collection_create.html.twig', [
            'form' => $form->createView(),
            'isCreate' => $isCreate,
            'userGroupCollection' => $userGroupCollection,
        ]);
    }

    #[Route('/kontrollpanel/brukergruppesamling', name: 'usergroup_collections', methods: ['GET'])]
    public function userGroupCollectionsAction()
    {
        $userGroupCollections = $this->em->getRepository(UserGroupCollection::class)->findAll();

        return $this->render('usergroup_collection/usergroup_collections.html.twig', [
            'userGroupCollections' => $userGroupCollections,
        ]);
    }

    #[Route('/kontrollpanel/brukergruppesamling/slett/{id}', name: 'usergroup_collection_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
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
