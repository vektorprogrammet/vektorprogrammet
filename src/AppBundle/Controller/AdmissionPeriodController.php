<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Form\Type\EditAdmissionPeriodType;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\CreateAdmissionPeriodType;
use Symfony\Component\HttpFoundation\JsonResponse;

class AdmissionPeriodController extends BaseController
{
    public function showAction()
    {
        // Finds the departmentId for the current logged in user
        $department = $this->getUser()->getDepartment();

        return $this->showByDepartmentAction($department);
    }

    public function showByDepartmentAction(Department $department)
    {
        $admissionPeriods = $this->getDoctrine()
            ->getRepository('AppBundle:AdmissionPeriod')
            ->findBy(array('department' => $department));


        // Renders the view with the variables
        return $this->render('admission_period_admin/index.html.twig', array(
            'admissionPeriods' => $admissionPeriods,
            'departmentName' => $department->getShortName(),
            'department' => $department
        ));
    }

    public function createAdmissionPeriodAction(Request $request, Department $department)
    {
        $admissionPeriod = new AdmissionPeriod();
        $form = $this->createForm(new CreateAdmissionPeriodType(), $admissionPeriod);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $admissionPeriod->setDepartment($department);

            $em = $this->getDoctrine()->getManager();
            $em->persist($admissionPeriod);
            $em->flush();

            return $this->redirectToRoute('admission_period_admin_show_by_department', array('id' => $department->getId()));
        }

        // Render the view
        return $this->render('admission_period_admin/create_admission_period.html.twig', array(
            'department' => $department,
            'form' => $form->createView(),
        ));
    }

    public function updateAdmissionPeriodAction(Request $request, AdmissionPeriod $admissionPeriod)
    {
        $form = $this->createForm(EditAdmissionPeriodType::class, $admissionPeriod);

        // Handle the form
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($admissionPeriod);
            $em->flush();

            return $this->redirectToRoute('admission_period_admin_show_by_department', array('id' => $admissionPeriod->getDepartment()->getId()));
        }

        return $this->render('admission_period_admin/edit_admission_period.html.twig', array(
            'form' => $form->createView(),
            'semesterName' => $admissionPeriod->getSemester()->getName(),
            'department' => $admissionPeriod->getDepartment(),
        ));
    }

    public function deleteAction(AdmissionPeriod $admissionPeriod)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($admissionPeriod);
        $em->flush();

        return new JsonResponse(array('success' => true));
    }
}
