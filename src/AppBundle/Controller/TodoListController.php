<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use AppBundle\Form\Type\CreateTodoItemInfoType;
use AppBundle\Model\TodoItemInfo;
use AppBundle\Entity\TodoItem;
use AppBundle\Service\TodoListService;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TodoListController extends BaseController
{
    public function showAction()
    {
        $todoListService = $this->get(TodoListService::class);
        $department = $this->getDepartmentOrThrow404();
        $semester = $this->getSemesterOrThrow404();

        $todosInOrder = $todoListService->getOrderedList($department, $semester);

        return $this->render("todo_list/todo_list.twig", array(
            'department' => $department,
            'semester' => $semester,
            'correctList' => $todosInOrder,
            'now' => new DateTime(),
        ));
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function createTodoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $todoListService = $this->get(TodoListService::class);
        $itemInfo = new TodoItemInfo();

        $form = $this->createForm(CreateTodoItemInfoType::class, $itemInfo, array(
            'validation_groups' => array('create_todoItemInfo'),
        ));


        $form->handleRequest($request);

        $department = $this->getDepartmentOrThrow404();
        $semester = $this->getSemesterOrThrow404();
        if ($form->isValid()) {
            $todoListService->generateEntities($itemInfo, $em);
            return $this->redirectToRoute('todo_list', ['department'=> $department->getId(), 'semester'=>$semester->getId()]);
        }

        return $this->render('todo_list/create_todo_element.html.twig', array(
            'form' => $form->createView(),
            'department' => $department,
            'semester' => $semester,
            'create_or_update_action' => 'Opprett',
            'create_or_update_title' => 'Opprett nytt gjøremål',
        ));
    }


    /**
     * @param TodoItem $item
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function editTodoAction(TodoItem $item, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $currentSemester = $em->getRepository('AppBundle:Semester')->findCurrentSemester();


        $todoListService = $this->get(TodoListService::class);
        $itemInfo = $todoListService->createTodoItemInfoFromItem($item, $currentSemester);


        $form = $this->createForm(CreateTodoItemInfoType::class, $itemInfo, array(
            'validation_groups' => array('edit_todoItemInfo'),
        ));
        $form->handleRequest($request);
        $department = $this->getDepartmentOrThrow404();
        $semester = $this->getSemesterOrThrow404();
        if ($form->isValid()) {
            $todoListService->editEntities($itemInfo, $currentSemester);
            return $this->redirectToRoute('todo_list', ['department'=> $department->getId(), 'semester'=>$semester->getId()]);
        }

        return $this->render('todo_list/create_todo_element.html.twig', array(
            'form' => $form->createView(),
            'department' => $department,
            'semester' => $semester,
            'create_or_update_action' => 'Endre',
            'create_or_update_title' => 'Endre eksisterende gjøremål',
        ));
    }

    /**
     * @param TodoItem $item
     * @param Semester $semester
     * @param Department $department
     * @return JsonResponse
     */
    public function completedAction(TodoItem $item, Semester $semester, Department $department)
    {
        $todoListService = $this->get(TodoListService::class);
        try {
            $todoListService->completedItem($item, $semester, $department);
            if ($item->isCompletedInSemesterByDepartment($semester, $department)) {
                $todoListService->toggleCompletedItem($item, $semester, $department);
                $response['todoCompleted'] = false;
            } else {
                $todoListService->toggleCompletedItem($item, $semester, $department);
                $response['todoCompleted'] = true;
            }
            $response['success'] = true;
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'code' => $e->getCode(),
                'cause' => 'Det oppstod en feil.',
            ];
        }
        return new JsonResponse($response);
    }

    /**
     * @param TodoItem $item
     * @return RedirectResponse
     */
    public function toggleAction(TodoItem $item)
    {
        $department = $this->getDepartmentOrThrow404();
        $semester = $this->getSemesterOrThrow404();
        $todoListService = $this->get(TodoListService::class);
        $todoListService->toggleCompletedItem($item, $semester, $department);
        return $this->redirectToRoute('todo_list', ['department'=> $department->getId(), 'semester'=>$semester->getId()]);
    }

    /**
     * @param TodoItem $item
     * @return RedirectResponse
     * @throws Exception
     */
    public function deleteTodoItemAction(TodoItem $item)
    {
        $semester = $this->getSemesterOrThrow404();
        $department = $this->getDepartmentOrThrow404();
        if ($semester === $this->getDoctrine()->getManager()->getRepository('AppBundle:Semester')->findCurrentSemester()) {
            $item->setDeletedAt(new DateTime());
        } else {
            $item->setDeletedAt($semester->getSemesterStartDate());
        }
        $this->getDoctrine()->getManager()->persist($item);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('todo_list', ['department'=> $department->getId(), 'semester'=>$semester->getId()]);
    }
}
