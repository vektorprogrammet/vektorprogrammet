<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use AppBundle\Entity\InfoMeeting;
use AppBundle\Form\Type\EditInfoMeetingType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class InfoMeetingController extends Controller
{
    public function showAction(Request $request, Department $department)
    {

        $infoMeeting = new InfoMeeting();

        $form = $this->createForm(EditInfoMeetingType::class, array(
            'department' => $department
        ));

        $form->handleRequest($request);

        if($form->isValid()) {
            $data = $form->getData();

            $infoMeeting->setTime($data['time']);
            $infoMeeting->setDate($data['date']);
            $infoMeeting->setRoom($data['room']);
            $infoMeeting->setExtra($data['extra']);
            $infoMeeting->setDepartment($department);

            if ($form->get('save')->isClicked()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($infoMeeting);
                $em->flush();
                $this->addFlash('success', 'Møtet ble lagret!');
            }

            if ($form->get('delete')->isClicked()) {
                $department->setInfoMeeting(null);
                $em = $this->getDoctrine()->getManager();
                $em->persist($department);
                $em->flush();
                $this->addFlash('success', 'Møtet ble slettet!');
            }

            return $this->redirectToRoute('control_panel');

        }

        return $this->render('info_meeting/info_meeting_admin.html.twig',
            array(
                'department' => $department,
                'form' => $form->createView()));
    }
}
