<?php

namespace AppBundle\Controller;


use AppBundle\Entity\SocialEvent;
use AppBundle\Form\Type\SocialEventType;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use AppBundle\Service\SocialEventManager;


use AppBundle\Form\Type\CreateTodoItemInfoType;
use Symfony\Component\HttpFoundation\JsonResponse;


class SocialEventController extends BaseController
{

    public function showAction()
    {

        $department = $this->getDepartmentOrThrow404();
        $semester = $this->getSemesterOrThrow404();

        $socialEventManager = $this->get(SocialEventManager::class);
        $SocialEventList = $socialEventManager->getOrderedList($department, $semester);


        return $this->render("social_event/social_event_list.twig", array(
            'department' => $department,
            'semester' => $semester,
            'SocialEventList' => $SocialEventList, /// THIIS
            'now' => new \DateTime(),
        ));
    }

    // --------------------- //



    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createSocialEventAction(Request $request)
    {
        #TODO SETUP ACCESS RULES
        /*
        if ($this->get(AccessControlService::class)->checkAccess("create_event")) {
        } else {
            $form = $this->createForm(SurveyType::class, $survey);
        }
        */


        $em = $this->getDoctrine()->getManager();

        $social_event = new SocialEvent();


        $form = $this->createForm(SocialEventType::class, $social_event);


        $form->handleRequest($request);

        $department = $this->getDepartmentOrThrow404();
        $semester = $this->getSemesterOrThrow404();

        if ($form->isValid()) {
            ## $this->ensureAccess($event);

            $em->persist($social_event);
            $em->flush();

            // Need some form of redirect. Will cause wrong database entries if the form is rendered again
            // after a valid submit, without remaking the form with up to date question objects from the database.
            //return $this->redirect($this->generateUrl('control_panel')); || Not in use
            return $this->redirectToRoute('social_event_show', ['department'=> $department->getId(), 'semester'=>$semester->getId()]);
        }

        return $this->render('social_event/social_event_create.html.twig', array(
            'form' => $form->createView(),
            'department' => $department,
            'semester' => $semester,
            'event' => $social_event
        ));
    }





    #TODO: functions yet to be implemented:
    //public function editSocialEventAction
    //public function deleteSocialEventAction
    //public function copySocialEventAction

}
