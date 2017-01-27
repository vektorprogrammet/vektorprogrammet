<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Semester;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Department;
use AppBundle\Entity\Substitute;
use AppBundle\Entity\Application;
use AppBundle\Form\Type\SubstituteType;

/**
 * SubstituteController is the controller responsible for substitute assistants,
 * such as showing and deleting substitutes.
 */
class SubstituteController extends Controller
{
    public function showAction(Request $request, Department $department = null){

        if ($department === null){
            $department = $this->getUser()->getDepartment();
        }
        $semester = $this->getDoctrine()->getRepository('AppBundle:Semester')->findOneBy(array('id' => $request->get('semester')));
        if ($semester === null){
            $semester = $this->getDoctrine()->getRepository('AppBundle:Semester')->findCurrentSemesterByDepartment($department);
        }

        $substitutes = $this->getDoctrine()->getRepository('AppBundle:Substitute')->findSubstitutesByDepartment($department);
        dump($substitutes);
        $semesters = $this->getDoctrine()->getRepository('AppBundle:Semester')->findAllSemestersByDepartment($department);



        return $this->render('substitute/index.html.twig', array(
            'substitutes' => $substitutes,
            'department' => $department,
            'semesters' => $semesters,
            'semester' => $semester,
        ));
    }

}
