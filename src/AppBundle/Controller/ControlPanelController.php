<?php

namespace AppBundle\Controller;

use AppBundle\Service\SbsData;

class ControlPanelController extends BaseController
{
    public function showAction()
    {
        $department = $this->getDepartmentOrThrow404();
        $semester = $this->getSemesterOrThrow404();
        $user = $this->getUser();

        $admissionPeriod = $this->getDoctrine()->getRepository('AppBundle:AdmissionPeriod')
            ->findOneByDepartmentAndSemester($department, $semester);



        $surveys = $this->getDoctrine()
            ->getRepository('AppBundle:Survey')
            ->findAllNotTakenByUserAndSemester($user, $semester);
        $isAvailableSurveys = !empty($surveys);


        // Return the view to be rendered
        return $this->render('control_panel/index.html.twig', array(
            'admissionPeriod' => $admissionPeriod,
            'isAvailableSurveys' => $isAvailableSurveys,
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
