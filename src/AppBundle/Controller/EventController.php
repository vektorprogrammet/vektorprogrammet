<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Event;
use AppBundle\Form\Type\EventType;
use Symfony\Component\HttpFoundation\Request;



class EventController extends BaseController
{


    public function createEventAction(Request $request)
    {
        $event = new Event();


        #TODO SETUP ACCESS RULES
        /*
        if ($this->get(AccessControlService::class)->checkAccess("create_event")) {
        } else {
            $form = $this->createForm(SurveyType::class, $survey);
        }
        */

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isValid()) {
            ## $this->ensureAccess($event);
            $em = $this->getDoctrine()->getManager();

            $em->persist($event);
            $em->flush();

            // Need some form of redirect. Will cause wrong database entries if the form is rendered again
            // after a valid submit, without remaking the form with up to date question objects from the database.
            return $this->redirect($this->generateUrl('control_panel'));
        }

        return $this->render('event/social_event_create.html.twig', array(
            'form' => $form->createView(),
            'event' => $event
        ));
    }


}
