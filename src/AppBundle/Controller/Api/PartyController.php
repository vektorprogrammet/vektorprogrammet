<?php

namespace AppBundle\Controller\Api;


use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Entity\Application;
use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class PartyController extends AbstractFOSRestController
{

    /**
     * @param Department $department
     *
     * @Route(
     *     "api/party/application_count/{department}/",
     *     methods={"GET"}
     * )
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws NotFoundHttpException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function ApplicationCountAction(Department $department = null)
    {
        $semester = $this->getDoctrine()->getRepository(Semester::class)->findCurrentSemester();
        $admissionPeriod = $this->getDoctrine()
            ->getRepository(AdmissionPeriod::class)
            ->findOneByDepartmentAndSemester($department, $semester);
        if ($admissionPeriod === null) {
            throw new NotFoundHttpException();
        }

        $applications = $this->getDoctrine()
            ->getRepository(Application::class)
            ->findByAdmissionPeriod($admissionPeriod);

        $view = $this->view(count($applications));
        return $this->handleView($view);
    }
}
