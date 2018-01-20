<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use AppBundle\Entity\InfoMeeting;
use AppBundle\Event\InfoMeetingEvent;
use AppBundle\Form\Type\InfoMeetingType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class InfoMeetingController extends Controller
{
    public function showAction(Request $request, Department $department)
    {
        $newMeeting = true;
        $infoMeeting = new InfoMeeting();
        if ($department->getInfoMeeting() !== null) {
            $newMeeting = false;
            $infoMeeting = $department->getInfoMeeting();
        }

        $form = $this->createForm(InfoMeetingType::class, $infoMeeting);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $infoMeeting->setDepartment($department);

            $em = $this->getDoctrine()->getManager();
            $em->persist($infoMeeting);
            $em->flush();
            $this->addFlash('success', 'Møtet ble lagret!');

            if ($newMeeting) {
                $this->get('event_dispatcher')->dispatch(InfoMeetingEvent::CREATED, new InfoMeetingEvent($infoMeeting));
            } else {
                $this->get('event_dispatcher')->dispatch(InfoMeetingEvent::EDITED, new InfoMeetingEvent($infoMeeting));
            }

            return $this->redirectToRoute('control_panel');
        }

        return $this->render('info_meeting/info_meeting_admin.html.twig',
            array(
                'department' => $department,
                'form' => $form->createView()));
    }

    public function deleteAction(Request $request, Department $department)
    {
        $infoMeeting = $department->getInfoMeeting();
        if ($infoMeeting !== null) {
            $em = $this->getDoctrine()->getManager();
            $department->setInfoMeeting(null);
            $em->persist($department);
            $em->remove($infoMeeting);
            $em->flush();
            $this->addFlash('success', 'Møtet ble slettet!');

            $this->get('event_dispatcher')->dispatch(InfoMeetingEvent::DELETED, new InfoMeetingEvent($infoMeeting));
        }

        return $this->redirectToRoute('control_panel');
    }
}
