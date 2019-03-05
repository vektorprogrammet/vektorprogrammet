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

        if ($form->isValid()) {
            $this->ensureAccess($changelog);
            $em = $this->getDoctrine()->getManager();
            $em->persist($changelog);
            $em->flush();

            dump($changelog);

            // Need some form of redirect. Will cause wrong database entries if the form is rendered again
            // after a valid submit, without remaking the form with up to date question objects from the database.
            return $this->redirect($this->generateUrl('surveys'));
        }

        return $this->render('changelog/changelog_create.html.twig',array(
            'form' => $form->createView(),
            'changelog' => $changelog,
        ));
    }
}






