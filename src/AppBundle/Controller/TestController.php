<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use AppBundle\Entity\ExecutiveBoard;
use AppBundle\Entity\Semester;
use AppBundle\Form\Type\CreateTodoItemInfoType;
use AppBundle\Form\Type\TestType;
use AppBundle\Model\TodoItemInfo;
use AppBundle\Entity\TodoItem;
use AppBundle\Service\TodoListService;
use DateTime;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TestController extends BaseController
{
    public function showAction(ExecutiveBoard $executiveBoard=null)
    {
        $execBoard = new ExecutiveBoard();
        $imgPathStart = "img/";
        $imgPathEnd = ".jpg";
        $form = $this->createForm(TestType::class, $execBoard);
        return $this->render("test.html.twig", ["form" => $form->createView(),
            "executiveBoard" => $execBoard,
            "startPath" => $imgPathStart,
            "endPath" => $imgPathEnd]);
    }


    /**
     * @Route("/test/show", name="show_image")
     * @Route("/test/edit", name="edit_image")
     * @param ExecutiveBoard $executiveBoard
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(ExecutiveBoard $executiveBoard = null, Request $request)
    {
        $form = $this->createForm(TestType::class, $executiveBoard);
        $form->handleRequest($request);

    }


}
