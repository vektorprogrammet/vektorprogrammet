<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use AppBundle\Form\Type\CreateToDoItemInfoType;
use AppBundle\Model\ToDoItemInfo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Service\ToDoListService;
use AppBundle\Entity\ToDoItem;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;

class ToDoListController extends Controller
{
    public function showAction()
    {

        $repository = $this->getDoctrine()->getRepository('AppBundle:ToDoItem');
        $toDoListService = $this->get('app.to_do_list_service');

        //TOOLBOX:
        //Note: Although the creation of the lists below could be moved to the service,
        //some of the lists are required in the twig file
        $em = $this->getDoctrine()->getManager();
        $department = $this->getUser()->getDepartment();
        $semester = $em->getRepository('AppBundle:Semester')->findCurrentSemesterByDepartment($department);

        $allToDoItems = $repository->findToDoListItemsBySemester($semester);
        $incompletedToDoItems = $toDoListService->getIncompletedToDoItems($allToDoItems, $semester, $department);
        $toDoShortDeadLines = $toDoListService->getToDoItemsWithShortDeadline($incompletedToDoItems);
        $toDoMandaoryNoDeadLine = $toDoListService->getMandatoryToDoItemsWithInsignificantDeadline($incompletedToDoItems, $semester);
        $toDoNonMandatoryNoDeadline = $toDoListService->getNonMandatoryToDoItemsWithInsignificantDeadline($incompletedToDoItems, $semester);
        $completedToDoListItems = $repository->findCompletedToDoListItems($semester);
        $correctOrderWithDeleted = array_merge($toDoShortDeadLines, $toDoMandaoryNoDeadLine, $toDoNonMandatoryNoDeadline, $completedToDoListItems);

        $correctOrder = array_filter($correctOrderWithDeleted, function (ToDoItem $a) {
            return $a->getDeletedAt() == null;
        });

        return $this->render("todo_list/toDoList.html.twig", array(
            'allToDoItems' => $allToDoItems,
            'completedToDoListItems' => $completedToDoListItems,
            'department' => $department,
            'semester' => $semester,
            'shortDeadlines' => $toDoShortDeadLines,
            'correctList' => $correctOrder,
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createToDoAction(Request $request)
    {
        $toDoListService = $this->get('app.to_do_list_service');
        $itemInfo = new ToDoItemInfo();

        $form = $this->createForm(CreateToDoItemInfoType::class, $itemInfo, array(
            'validation_groups' => array('create_toDoItemInfo'),
        ));

        // Handle the form
        $form->handleRequest($request);

        // The fields of the form is checked if they contain the correct information
        if ($form->isValid()) {
            $toDoListService->generateEntities($itemInfo);
            return $this->redirectToRoute('to_do_list');
        }

        // Render the view
        return $this->render('todo_list/create_todo_element.html.twig', array(
            'form' => $form->createView(),
        ));
    }



    public function editToDoAction(ToDoItem $item, Request $request)
    {
        $toDoListService = $this->get('app.to_do_list_service');
        $itemInfo = new ToDoItemInfo();



        $form = $this->createForm(CreateToDoItemInfoType::class, $itemInfo, array(
            'validation_groups' => array('edit_toDoItemInfo'),
        ));

        // Handle the form
        $form->handleRequest($request);

        // The fields of the form is checked if they contain the correct information
        if ($form->isValid()) {
            $toDoListService->generateEntities($itemInfo);
            return $this->redirectToRoute('to_do_list');
        }

        // Render the view
        return $this->render('todo_list/create_todo_element.html.twig', array(
            'form' => $form->createView(),
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

    public function toggleAction(ToDoItem $item, Request $request)
    {
        $departmentID = $request->request->get('department');
        $semesterID = $request->request->get('semester');
        $department = $this->getDoctrine()->getRepository('AppBundle:Department')->find($departmentID);
        $semester = $this->getDoctrine()->getRepository('AppBundle:Semester')->find($semesterID);

        dump($department);
        dump($semester);
        $toDoListService = $this->get('app.to_do_list_service');
        $toDoListService->toggleCompletedItem($item, $semester, $department);
        return $this->redirectToRoute('to_do_list');
    }

    public function deleteTodoItemAction(ToDoItem $item)
    {
        $item->setDeletedAt(new \DateTime());
        $this->getDoctrine()->getManager()->persist($item);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('to_do_list');
    }
}
