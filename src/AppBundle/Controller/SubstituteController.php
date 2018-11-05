<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Application;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use AppBundle\Form\Type\ModifySubstituteType;

/**
 * SubstituteController is the controller responsible for substitute assistants,
 * such as showing, modifying and deleting substitutes.
 */
class SubstituteController extends BaseController
{
    public function showAction()
    {
        // No department specified, get the user's department and call showBySemester with
        // either current or latest semester for that department
        $department = $this->getDepartmentOrThrow404();
        $semester = $this->getSemesterOrThrow404();

        $admissionPeriod = $this->getDoctrine()->getRepository('AppBundle:AdmissionPeriod')
            ->findOneByDepartmentAndSemester($department, $semester);

        $substitutes = null;
        if ($admissionPeriod !== null) {
            $substitutes = $this->getDoctrine()
                ->getRepository('AppBundle:Application')
                ->findSubstitutesByAdmissionPeriod($admissionPeriod);
        }

        return $this->render('substitute/index.html.twig', array(
            'substitutes' => $substitutes,
            'semester' => $semester,
            'department' => $department,
        ));
    }

    public function showModifyFormAction(Request $request, Application $application)
    {
        // Only substitutes should be modified with this form
        if (!$application->isSubstitute()) {
            throw new BadRequestHttpException();
        }

        $department = $application->getUser()->getDepartment();

        $form = $this->createForm(ModifySubstituteType::class, $application, array(
            'department' => $department,
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($application);
            $em->flush();

            // Need some form of redirect. Will cause wrong database entries if the form is rendered again
            // after a valid submit, without remaking the form with up to date question objects from the database.
            return $this->redirect($this->generateUrl('substitute_show', array(
                'semester' => $application->getSemester()->getId(),
                'department' => $department->getId(),
            )));
        }

        return $this->render('substitute/modify_substitute.twig', array(
            'application' => $application,
            'form' => $form->createView(),
        ));
    }

    public function deleteSubstituteByIdAction(Application $application)
    {
        $application->setSubstitute(false);

        $em = $this->getDoctrine()->getManager();
        $em->persist($application);
        $em->flush();

        // Redirect to substitute page, set semester to that of the deleted substitute
        return $this->redirectToRoute('substitute_show', array(
            'semester' => $application->getSemester()->getId(),
            'department' => $application->getAdmissionPeriod()->getDepartment()->getid(),
        ));
    }

    public function createSubstituteFromApplicationAction(Application $application)
    {
        if ($application->isSubstitute()) {
            // User is already substitute
            throw new BadRequestHttpException();
        }
        $application->setSubstitute(true);

        $em = $this->getDoctrine()->getManager();
        $em->persist($application);
        $em->flush();

        // Redirect to substitute page, set semester to that of the newly added substitute
        return $this->redirectToRoute('substitute_show', array(
            'semester' => $application->getSemester()->getId(),
            'department' => $application->getAdmissionPeriod()->getDepartment()->getId(),
        ));
    }
}
