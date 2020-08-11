<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Entity\Application;
use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class PartyController extends AbstractFOSRestController
{
    const NUM_APPLICATIONS = 5;

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
    public function ApplicationCountAction(Department $department)
    {
        $applications = $this->getApplications($department);
        $view = $this->view(count($applications));
        return $this->handleView($view);
    }

    /**
     * @param Department $department
     *
     * @Route(
     *     "api/party/newest_applications/{department}/",
     *     methods={"GET"}
     * )
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function NewestApplicationsAction(Department $department)
    {
        $applications = $this->getApplications($department);
        $newestApplications = array_slice($applications, 0, self::NUM_APPLICATIONS, true);

        $normalizer = new ObjectNormalizer();
        $encoder = new JsonEncoder();
        $serializer = new Serializer([$normalizer], [$encoder]);

        $newestApplications = $serializer->normalize(
            $newestApplications,
            null,
            ['attributes' =>
                [
                    'id',
                    'user' => [
                        'firstName',
                        'lastName',
                        'fieldOfStudy' =>
                        [
                            'name',
                            'shortName'
                        ],
                    ],
                    'yearOfStudy',
                ]
            ]
        );
        $view = $this->view($newestApplications);
        return $this->handleView($view);
    }

    /**
     * @param Department $department
     *
     * @Route(
     *     "api/party/deadline/{department}/",
     *     methods={"GET"}
     * )
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function ApplicationDeadlineAction(Department $department)
    {
        $admissionPeriod = $this->getAdmissionPeriod($department);
        $deadline = $admissionPeriod->getEndDate();
        $view = $this->view($deadline->format('Y-m-d H:i:s'));
        return $this->handleView($view);
    }

    /**
     * @param Department $department
     *
     * @return Application[]
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function getApplications(Department $department)
    {
        $admissionPeriod = $this->getAdmissionPeriod($department);

        return $this->getDoctrine()
            ->getRepository(Application::class)
            ->findByAdmissionPeriod($admissionPeriod);
    }

    /**
     * @param Department $department
     *
     * @return AdmissionPeriod
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function getAdmissionPeriod(Department $department): AdmissionPeriod
    {
        $semester = $this->getDoctrine()->getRepository(Semester::class)->findCurrentSemester();
        $admissionPeriod = $this->getDoctrine()
            ->getRepository(AdmissionPeriod::class)
            ->findOneByDepartmentAndSemester($department, $semester);
        if ($admissionPeriod === null) {
            throw new NotFoundHttpException();
        }
        return $admissionPeriod;
    }
}
