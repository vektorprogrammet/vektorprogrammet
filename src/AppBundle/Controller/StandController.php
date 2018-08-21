<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Semester;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class StandController extends Controller
{
    /**
     * @Route("/kontrollpanel/stand/{id}", name="stand_by_semester")
     * @Route("/kontrollpanel/stand", name="stand")
     *
     * @param Semester|null $semester
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Semester $semester = null)
    {
        if ($semester === null) {
            $semester = $this->getUser()->getDepartment()->getCurrentOrLatestSemester();
        }
        $department = $semester->getDepartment();

        $admissionStatistics = $this->get('app.admission_statistics');

        $subscribers = $this->getDoctrine()->getRepository('AppBundle:AdmissionSubscriber')->findFromWebByDepartment($department);
        $subscribersInSemester = $this->getDoctrine()->getRepository('AppBundle:AdmissionSubscriber')->findFromWebBySemester($semester);
        $subData = $admissionStatistics->generateGraphDataFromSubscribersInSemester($subscribersInSemester, $semester);

        $applications = $this->getDoctrine()->getRepository('AppBundle:Application')->findByDepartment($department);
        $applicationsInSemester = $this->getDoctrine()->getRepository('AppBundle:Application')->findBySemester($semester);
        $appData = $admissionStatistics->generateGraphDataFromApplicationsInSemester($applicationsInSemester, $semester);


        return $this->render('stand_admin/stand.html.twig', [
            'semester' => $semester,
            'subscribers' => $subscribers,
            'subscribers_in_semester' => $subscribersInSemester,
            'subData' => $subData,
            'applications' => $applications,
            'applications_in_semester' => $applicationsInSemester,
            'appData' => $appData,
        ]);
    }
}
