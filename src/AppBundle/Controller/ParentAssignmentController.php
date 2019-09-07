<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\ParentAssignmentType;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Service\GeoLocation;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParentAssignmentController extends BaseController
{
    public function showAction(Request $request)
    {
        //Hvorfor lastes siden inn med redigeringen på?
        //$parentAssigned = new ParentAssigned() Noe må persistes? Hva?
        $form = $this->createForm(ParentAssignmentType::class);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            //$em->persist($parentAssigned);
            $em->flush();
            return $this->redirect($this->generateUrl('')); //Her kan jeg redirecte tilbake til Sivert sin foreldre-side
        }

        return $this->render('parents/parent-assignment.html.twig', array(
            'form' => $form->createView(),
));

    }

};
