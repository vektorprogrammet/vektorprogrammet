<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Semester;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Department;
use AppBundle\Entity\Application;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * SubstituteController is the controller responsible for substitute assistants,
 * such as showing and deleting substitutes.
 */
class SubstituteController extends Controller
{
    public function showAction()
    {
        $department = $this->getUser()->getDepartment();

        return $this->showByDepartmentAction($department);
    }

    public function showBySemesterAction(Semester $semester)
    {
        //$substitutes = $this->getDoctrine()->getRepository('AppBundle:Substitute')->findSubstitutesBySemester($semester);
        $substitutes = $this->getDoctrine()->getRepository('AppBundle:Application')->findSubstitutesBySemester($semester);
        $semesters = $this->getDoctrine()->getRepository('AppBundle:Semester')->findAllSemestersByDepartment($semester->getDepartment());

        return $this->render('substitute/index.html.twig', array(
            'substitutes' => $substitutes,
            'semesters' => $semesters,
            'semester' => $semester,
        ));
    }

    public function showByDepartmentAction(Department $department)
    {
        $semester = $this->getDoctrine()->getRepository('AppBundle:Semester')->findCurrentSemesterByDepartment($department);
        if ($semester === null) {
            $semester = $this->getDoctrine()->getRepository('AppBundle:Semester')->findLatestSemesterByDepartmentId($department);
        }

        return $this->showBySemesterAction($semester);
    }

    public function deleteSubstituteByIdAction(Application $application)
    {
        $application->setSubstitute(false);

        $em = $this->getDoctrine()->getManager();
        $em->persist($application);
        $em->flush();

        return $this->redirectToRoute('substitute_show_by_semester', array('semester' => $application->getSemester()->getId()));
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

        return $this->redirectToRoute('substitute_show_by_semester', array('semester' => $application->getSemester()->getId()));
    }
}
