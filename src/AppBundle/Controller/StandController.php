<?php

namespace AppBundle\Controller;

use AppBundle\Service\AdmissionStatistics;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class StandController extends BaseController
{

    /**
     * @Route("/kontrollpanel/stand", name="stand")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function indexAction()
    {
        $department = $this->getDepartmentOrThrow404();
        $semester = $this->getSemesterOrThrow404();

        $admissionStatistics = $this->get(AdmissionStatistics::class);

        $subscribers = $this->getDoctrine()->getRepository('AppBundle:AdmissionSubscriber')->findFromWebByDepartment($department);
        $subscribersInDepartmentAndSemester = $this->getDoctrine()->getRepository('AppBundle:AdmissionSubscriber')
            ->findFromWebByDepartmentAndSemester($department, $semester);
        $subData = $admissionStatistics->generateGraphDataFromSubscribersInSemester($subscribersInDepartmentAndSemester, $semester);

        $applications = $this->getDoctrine()->getRepository('AppBundle:Application')->findByDepartment($department);
        $admissionPeriod = $this->getDoctrine()->getRepository('AppBundle:AdmissionPeriod')
            ->findOneByDepartmentAndSemester($department, $semester);
        $applicationsInSemester = [];
        if ($admissionPeriod !== null) {
            $applicationsInSemester = $this->getDoctrine()
                ->getRepository('AppBundle:Application')
                ->findByAdmissionPeriod($admissionPeriod);
        }
        $appData = $admissionStatistics->generateGraphDataFromApplicationsInAdmissionPeriod($applicationsInSemester, $admissionPeriod);


        return $this->render('stand_admin/stand.html.twig', [
            'department' => $department,
            'semester' => $semester,
            'subscribers' => $subscribers,
            'subscribers_in_semester' => $subscribersInDepartmentAndSemester,
            'subData' => $subData,
            'applications' => $applications,
            'applications_in_semester' => $applicationsInSemester,
            'appData' => $appData,
        ]);
    }
}
