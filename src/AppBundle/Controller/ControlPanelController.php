<?php

namespace AppBundle\Controller;

use AppBundle\Service\SbsData;

class ControlPanelController extends BaseController
{
    public function showAction()
    {
        $departments = $this->getDoctrine()->getRepository('AppBundle:Department')->findActive();

        // Return the view to be rendered
        return $this->render('control_panel/index.html.twig', array(
            'departments' => $departments,
        ));
    }

    public function showSBSAction()
    {
        $sbsData = $this->get(SbsData::class);
        $currentAdmissionPeriod = $this->getUser()->getDepartment()->getCurrentAdmissionPeriod();

        if ($currentAdmissionPeriod) {
            $sbsData->setAdmissionPeriod($currentAdmissionPeriod);
        }

        // Return the view to be rendered
        return $this->render('control_panel/sbs.html.twig', array(
            'data' => $sbsData,
        ));
    }
}
