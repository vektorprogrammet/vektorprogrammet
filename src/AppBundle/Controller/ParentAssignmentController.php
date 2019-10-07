<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\ParentAssignmentType;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\ParentAssignment;

class ParentAssignmentController extends BaseController
{
    public function showAction(Request $request)
    {
        $parentAssigned = new ParentAssignment();
        $form = $this->createForm(ParentAssignmentType::class, $parentAssigned);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($parentAssigned);
            $em->flush();
            return $this->redirect($this->generateUrl('foreldre'));
        }

        return $this->render('parents/parent-assignment.html.twig', array(
            'form' => $form->createView(),
));

    }

};
