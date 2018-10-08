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

        //$this->user = $this->getUser();
        //$dep = $this->user->getDepartment();

        $repository = $this->getDoctrine()->getRepository('AppBundle:ToDoItem');
        $toDoListService = $this->get('app.to_do_list_service');

        //TOOLBOX:
        $em = $this->getDoctrine()->getManager();
        $department = $this->getUser()->getDepartment();
        $semester = $em->getRepository('AppBundle:Semester')->findCurrentSemesterByDepartment($department);
        //elif user = superadmin, Dropdownmenu
        $allToDoItems = $repository->findToDoListItemsBySemester($semester);
        $mandatoryToDoItems = $toDoListService->getMandatoryToDoItems($allToDoItems);
        //$toDoItemsWithDeadLines = $toDoListService->getToDoItemsWithDeadlines($allToDoItems);
        //$testone = $toDoListService->getCompletedToDoItems();

        $toDoItemsWithDeadLines = $repository->findToDoListItemsWithDeadLines($semester);
        $completedToDoListItems = $repository->findCompletedToDoListItems($semester);
        //$toDoListService->getCompletedToDoItems($allToDoItems);

        $incompletedToDoItems = $toDoListService->getIncompletedToDoItems($allToDoItems, $semester, $department);
        $toDoShortDeadLines = $toDoListService->getToDoItemsWithShortDeadline($incompletedToDoItems);
        $toDoMandaoryNoDeadLine = $toDoListService->getMandatoryToDoItemsWithInsignificantDeadline($incompletedToDoItems);
        $toDoNonMandatoryNoDeadline = $toDoListService->getNonMandatoryToDoItemsWithInsignificantDeadline($incompletedToDoItems);
        $completedToDoListItems = $repository->findCompletedToDoListItems($semester);

        $deletedItems = $toDoListService->getDeletedToDoItems($allToDoItems);



        $showToDoItems = $mandatoryToDoItems;



        //$mylist = $toDoListService->getMyToDoItems($dep);

        return $this->render("todo_list/toDoList.html.twig", array(
            'allToDoItems' => $allToDoItems,
            'mandatoryToDoItems' => $toDoMandaoryNoDeadLine,
            'toDoWithDeadline' => $toDoItemsWithDeadLines,
            'completedToDoListItems' => $completedToDoListItems,
            'department' => $department,
            'semester' => $semester,
            'shortDeadlines' => $toDoShortDeadLines,
            'nonMandatoryToDoItems' => $toDoNonMandatoryNoDeadline,
            'deletedItems' => $deletedItems,

        ));
    }


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

    //Maybe move to service?
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
}
