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
        if ($department->getInfoMeeting() !== null) {
            $infoMeeting = $department->getInfoMeeting();
        }

        $form = $this->createForm(EditInfoMeetingType::class, $infoMeeting);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $infoMeeting->setDepartment($department);

            $em = $this->getDoctrine()->getManager();
            $em->persist($infoMeeting);
            $em->flush();
            $this->addFlash('success', 'Møtet ble lagret!');

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
        }

        return $this->redirectToRoute('control_panel');
    }
}
