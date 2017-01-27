<?php

namespace AppBundle\Controller;

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
    public function showAction(Request $request, $id){
        dump($id);

        $department = $this->getDoctrine()->getRepository('AppBundle:Department')->findDepartmentById($id);
        if (is_null($id)){
            $department = $this->getUser()->getDepartment();
            dump($department);
        }
        dump($department);


        $semester =

        $substitutes = $this->getDoctrine()->getRepository('AppBundle:Substitute')->findSubstitutesByDepartment($department);

        $semesters = $this->getDoctrine()->getRepository('AppBundle:Semester')->findAllSemestersByDepartment($department);



        return $this->render('substitute/index.html.twig', array(
            'substitutes' => $substitutes,
            'department' => $department,
            'semesters' => $semesters,
            'semester' => $semester,
        ));
    }

}
