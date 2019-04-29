<?php

namespace AppBundle\Controller;


use AppBundle\Entity\SocialEvent;
use AppBundle\Form\Type\SocialEventType;
use Symfony\Component\HttpFoundation\Request;



class SocialEventController extends BaseController
{


    public function createSocialEventAction(Request $request)
    {
        $social_event = new SocialEvent();


        #TODO SETUP ACCESS RULES
        /*
        if ($this->get(AccessControlService::class)->checkAccess("create_event")) {
        } else {
            $form = $this->createForm(SurveyType::class, $survey);
        }
        */

        $form = $this->createForm(SocialEventType::class, $social_event);
        $form->handleRequest($request);

        if ($form->isValid()) {
            ## $this->ensureAccess($event);
            $em = $this->getDoctrine()->getManager();

            $em->persist($social_event);
            $em->flush();

            // Need some form of redirect. Will cause wrong database entries if the form is rendered again
            // after a valid submit, without remaking the form with up to date question objects from the database.
            return $this->redirect($this->generateUrl('control_panel'));
        }

        return $this->render('social_event/social_event_create.html.twig', array(
            'form' => $form->createView(),
            'event' => $social_event
        ));
    }

    #TODO: functions yet to be implemented:
    //public function editSocialEventAction
    //public function deleteSocialEventAction
    //public function copySocialEventAction

}
