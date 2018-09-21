<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Service\ToDoListService;
use AppBundle\Entity\ToDoItem;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;

class ToDoListController extends Controller
{
    private $user;

    public function showAction(){

        //$this->user = $this->getUser();
        //$dep = $this->user->getDepartment();

        $repository = $this->getDoctrine()->getRepository('AppBundle:ToDoItem');
        $toDoListService = $this->get('app.to_do_list_service');

        //TOOLBOX:
        $em = $this->getDoctrine()->getManager();
        $department = $this->getUser()->getDepartment();
        $semester = $em->getRepository('AppBundle:Semester')->findCurrentSemesterByDepartment($department);
        $allToDoItems = $repository->findToDoListItemsBySemester($semester);
        $mandatoryToDoItems = $toDoListService->getMandatoryToDoItems($allToDoItems);
        //$toDoItemsWithDeadLines = $toDoListService->getToDoItemsWithDeadlines($allToDoItems);
        //$testone = $toDoListService->getCompletedToDoItems();

        $toDoItemsWithDeadLines = $repository->findToDoListItemsWithDeadLines($semester);
        $completedToDoListItems = $repository->findCompletedToDoListItems($semester);
            //$toDoListService->getCompletedToDoItems($allToDoItems);

        $incompletedToDoItems = $toDoListService->getIncompletedToDoItems($allToDoItems, $semester);
        $toDoShortDeadLines = $toDoListService->getToDoItemsWithShortDeadline($incompletedToDoItems);
        $toDoMandaoryNoDeadLine = $toDoListService->getMandatoryToDoItemsWithInsignificantDeadline($incompletedToDoItems);
        $toDoNonMandatoryNoDeadline = $toDoListService->getNonMandatoryToDoItemsWithInsignificantDeadline($incompletedToDoItems);
        $completedToDoListItems = $repository->findCompletedToDoListItems($semester);




        $showToDoItems = $mandatoryToDoItems;



        //$mylist = $toDoListService->getMyToDoItems($dep);

        return $this->render("to_do_list/toDoList.html.twig", array(
            'allToDoItems' => $allToDoItems,
            'mandatoryToDoItems' => $toDoMandaoryNoDeadLine,
            'toDoWithDeadline' => $toDoItemsWithDeadLines,
            'completedToDoListItems' => $completedToDoListItems,
            'department' => $department,
            'shortDeadlines' => $toDoShortDeadLines,
            'nonMandatoryToDoItems' => $toDoNonMandatoryNoDeadline,

        ));


    }

}