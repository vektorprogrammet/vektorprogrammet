<?php


namespace AppBundle\Controller;

use AppBundle\Entity\ChangeLogItem;
use AppBundle\Form\Type\ChangeLogType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class ChangeLogController extends BaseController
{


    public function createChangeLogAction(Request $request)
    {
        $changelog = new ChangeLogItem();
        $form = $this->createForm(ChangeLogType::class);
        $form->handleRequest($request);

        return $this->render('changelog/changelog_create.html.twig',array(
            'form' => $form->createView(),
            'changelog' => $changelog,
        ));
    }
}






