<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ControlPanelController extends Controller
{
    public function showAction()
    {
        $departments = $this->getDoctrine()->getRepository('AppBundle:Department')->findAll();

        // Return the view to be rendered
        return $this->render('control_panel/index.html.twig', array(
            'departments' => $departments,
        ));
    }

    public function showSBSAction()
    {
        $sbsData = $this->get('app.sbs_data');
        $currentSemester = $this->getUser()->getDepartment()->getCurrentSemester();

        if ($currentSemester) {
            $sbsData->setSemester($currentSemester);
        }

        // Return the view to be rendered
        return $this->render('control_panel/sbs.html.twig', array(
            'data' => $sbsData,
        ));
    }
}
