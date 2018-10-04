<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\CreateToDoItemInfoType;
use AppBundle\Model\ToDoItemInfo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Service\ToDoListService;
use AppBundle\Entity\ToDoItem;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;

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


    public function createToDoAction(Request $request)
    {
        /*
        if (!$this->isGranted(Roles::TEAM_LEADER) || $department === null) {
            $department = $this->getUser()->getDepartment();
        }*/

        // Create the user object
        //$item = new ToDoItem();
        //$item->setCreatedAt(new \DateTime());

        $toDoListService = $this->get('app.to_do_list_service');

        $itemInfo = new ToDoItemInfo();

        $form = $this->createForm(CreateToDoItemInfoType::class, $itemInfo, array(
            'validation_groups' => array('create_toDoItemInfo'),
            //'toDoItemInfo' => $itemInfo,
        ));


        // Handle the form
        $form->handleRequest($request);


        // The fields of the form is checked if they contain the correct information
        if ($form->isValid()) {/*

            $role = $this->getDoctrine()->getRepository('AppBundle:Role')->findByRoleName(Roles::ASSISTANT);
            $user->addRole($role);*/
            $toDoListService->generateEntities($itemInfo);
            //$itemInfo->generateEntities();
            //service->generateItems(itemInfo);
            //$item = $itemInfo->getToDoItem();
            //$em = $this->getDoctrine()->getManager();
            //$em->persist($item);
            //$em->flush();

            /*$this->get('app.user.registration')->sendActivationCode($user);*/

            return $this->redirectToRoute('to_do_list');
        }



        // Render the view
        return $this->render('to_do_list/create_todo_element.html.twig', array(
            'form' => $form->createView(),
            //'department' => null,
        ));
    }
}
