<?php

namespace AppBundle\Controller;

use AppBundle\Entity\SocialEvent;
use AppBundle\Form\Type\SocialEventType;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Service\SocialEventManager;

class SocialEventController extends BaseController
{
    public function showAction()
    {
        $department = $this->getDepartmentOrThrow404();
        $semester = $this->getSemesterOrThrow404();

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AppBundle:SocialEvent');
        $SocialEventList = $repository->findSocialEventsBySemesterAndDepartment($semester, $department);


        return $this->render("social_event/social_event_list.twig", array(
            'department' => $department,
            'semester' => $semester,
            'SocialEventList' => $SocialEventList,
            'now' => new \DateTime(),
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createSocialEventAction(Request $request)
    {
        $department = $this->getDepartmentOrThrow404();
        $semester = $this->getSemesterOrThrow404();
        $em              = $this->getDoctrine()->getManager();
        $socialEvent    = new SocialEvent();
        $user            = $this->getUser();

        $form = $this->createForm(SocialEventType::class, $socialEvent, array(
            'department'        => $department,
            'semester'          => $semester,
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($socialEvent);
            $em->flush();
            return $this->redirectToRoute('social_event_show', ['department'=> $department->getId(), 'semester'=>$semester->getId()]);
        }

        return $this->render('social_event/social_event_create.html.twig', array(
            'form' => $form->createView(),
            'department' => $department,
            'semester' => $semester,
            'event' => $socialEvent
        ));
    }

    public function editSocialEventAction(SocialEvent $social_event, Request $request)
    {
        $form = $this->createForm(SocialEventType::class, $social_event, array(
            'department'        => $social_event->getDepartment(),
            'semester'          => $social_event->getSemester(),
        ));
        $form->handleRequest($request);

        $department = $this->getDepartmentOrThrow404();
        $semester = $this->getSemesterOrThrow404();
        $em = $this->getDoctrine()->getManager();
        if ($form->isValid()) {
            $em->persist($social_event);
            $em->flush();
            return $this->redirectToRoute('social_event_show', ['department'=> $department->getId(), 'semester'=>$semester->getId()]);
        }

        return $this->render('social_event/social_event_create.html.twig', array(
            'form' => $form->createView(),
            'department' => $department,
            'semester' => $semester,
            'event' => $social_event,
        ));
    }

    public function deleteSocialEventAction(SocialEvent $event)
    {
        # NOTE: this function will permanently remove the event.
        $semester = $this->getSemesterOrThrow404();
        $department = $this->getDepartmentOrThrow404();

        $em = $this->getDoctrine()->getManager();
        $em->remove($event);
        $em->flush();

        return $this->redirectToRoute('social_event_show', ['department'=> $department->getId(), 'semester'=>$semester->getId()]);
    }
}
