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

        $infomeeting = new InfoMeeting();

        $form = $this->createForm(EditInfoMeetingType::class, $infomeeting, array(
            'department' => $department
        ));

        $form->handleRequest($request);

        return $this->render('info_meeting/info_meeting_admin.html.twig',
            array(
                'department' => $department,
                'form' => $form->createView()));
    }
}
