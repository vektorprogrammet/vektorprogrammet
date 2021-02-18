<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Entity\AdmissionSubscriber;
use AppBundle\Entity\Application;
use AppBundle\Service\AdmissionStatistics;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StandController extends BaseController
{

    /**
     * @Route("/kontrollpanel/stand", name="stand")
     *
     * @param Request $request
     * @return Response
     * @throws NonUniqueResultException
     */
    public function indexAction(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);

        $admissionStatistics = $this->get(AdmissionStatistics::class);

        $subscribers = $this->getDoctrine()->getRepository(AdmissionSubscriber::class)->findFromWebByDepartment($department);
        $subscribersInDepartmentAndSemester = $this->getDoctrine()->getRepository(AdmissionSubscriber::class)
            ->findFromWebByDepartmentAndSemester($department, $semester);
        $subData = $admissionStatistics->generateGraphDataFromSubscribersInSemester($subscribersInDepartmentAndSemester, $semester);

        $applications = $this->getDoctrine()->getRepository(Application::class)->findByDepartment($department);
        $admissionPeriod = $this->getDoctrine()->getRepository(AdmissionPeriod::class)
            ->findOneByDepartmentAndSemester($department, $semester);
        $applicationsInSemester = [];
        if ($admissionPeriod !== null) {
            $applicationsInSemester = $this->getDoctrine()
                ->getRepository(Application::class)
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
