<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use AppBundle\Form\Type\CreateToDoItemInfoType;
use AppBundle\Model\ToDoItemInfo;
use AppBundle\Entity\ToDoItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ToDoListController extends BaseController
{
    public function showAction()
    {
        $toDoListService = $this->get('app.to_do_list_service');
        $department = $this->getDepartmentOrThrow404();
        $semester = $this->getSemesterOrThrow404();

        $toDosInOrder = $toDoListService->getCorrectList($department, $semester);

        return $this->render("todo_list/toDoList.html.twig", array(
            'department' => $department,
            'semester' => $semester,
            'correctList' => $toDosInOrder,
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createToDoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $toDoListService = $this->get('app.to_do_list_service');
        $itemInfo = new ToDoItemInfo();

        $form = $this->createForm(CreateToDoItemInfoType::class, $itemInfo, array(
            'validation_groups' => array('create_toDoItemInfo'),
        ));

        // Handle the form
        $form->handleRequest($request);

        // The fields of the form is checked if they contain the correct information
        if ($form->isValid()) {
            $toDoListService->generateEntities($itemInfo, $em);
            return $this->redirectToRoute('to_do_list');
        }

        // Render the view
        return $this->render('todo_list/create_todo_element.html.twig', array(
            'form' => $form->createView(),
            'create_or_update_action' => 'Opprett',
            'create_or_update_title' => 'Opprett nytt gjøremål',
        ));
    }


    /**
     * @param ToDoItem $item
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function editToDoAction(ToDoItem $item, Request $request)
    {
        //Send inn via navigator. For now:
        $em = $this->getDoctrine()->getManager();
        $semester = $em->getRepository('AppBundle:Semester')->findCurrentSemester();


        $toDoListService = $this->get('app.to_do_list_service');
        $itemInfo = $toDoListService->createToDoItemInfoFromItem($item, $semester);


        $form = $this->createForm(CreateToDoItemInfoType::class, $itemInfo, array(
            'validation_groups' => array('edit_toDoItemInfo'),
        ));

        // Handle the form
        $form->handleRequest($request);

        // The fields of the form is checked if they contain the correct information
        if ($form->isValid()) {
            $toDoListService->editEntities($itemInfo, $semester);
            return $this->redirectToRoute('to_do_list');
        }

        // Render the view
        return $this->render('todo_list/create_todo_element.html.twig', array(
            'form' => $form->createView(),
            'create_or_update_action' => 'Endre',
            'create_or_update_title' => 'Endre eksisterende gjøremål',
        ));
    }

    /**
     * Set/unset completed status on the given item.
     * This method is intended to be called by an Ajax request.
     *
     * @param ToDoItem $item
     *
     * @return JsonResponse
     */
    public function completedAction(ToDoItem $item, Semester $semester, Department $department)
    {
        $toDoListService = $this->get('app.to_do_list_service');
        try {
            $toDoListService->completedItem($item, $semester, $department);
            if ($item->isCompletedInSemesterByDepartment($semester, $department)) {
                $toDoListService->toggleCompletedItem($item, $semester, $department);
                $response['toDoCompleted'] = false;
            } else {
                $toDoListService->toggleCompletedItem($item, $semester, $department);
                $response['toDoCompleted'] = true;
            }
            $response['success'] = true;

        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'code' => $e->getCode(),
                'cause' => 'Det oppstod en feil.',
            ];
        }
        return new JsonResponse($response);
    }

    public function toggleAction(ToDoItem $item)
    {
        $department = $this->getDepartmentOrThrow404();
        $semester = $this->getSemesterOrThrow404();

        dump($department);
        dump($semester);
        $toDoListService = $this->get('app.to_do_list_service');
        $toDoListService->toggleCompletedItem($item, $semester, $department);
        return $this->redirectToRoute('to_do_list', ['department'=> $department->getId(), 'semester'=>$semester->getId()]);
    }

    public function deleteTodoItemAction(ToDoItem $item)
    {
        $item->setDeletedAt(new \DateTime());
        $this->getDoctrine()->getManager()->persist($item);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('to_do_list');
    }
}
