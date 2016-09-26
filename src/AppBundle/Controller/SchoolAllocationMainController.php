<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Application;
use AppBundle\Entity\Semester;
use Symfony\Component\HttpFoundation\Request;

class SchoolAllocationMainController extends Controller
{
    public function showAction(Request $request, $departmentId = null)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if (null === $departmentId) {
            $departmentId = $user->getFieldOfStudy()->getDepartment()->getId();
        }
        $currentSemester = $this->getDoctrine()->getRepository('AppBundle:Semester')->findCurrentSemesterByDepartment($departmentId);
        $applications = $this->getDoctrine()->getRepository('AppBundle:Application')->findAllAllocatableApplicationsBySemester($currentSemester);
        $allCurrentSchoolCapacities = $this->getDoctrine()->getRepository('AppBundle:SchoolCapacity')->findBySemester($currentSemester);
        $allCurrentSchoolCapacities = $this->getDoctrine()->getRepository('AppBundle:SchoolCapacity')->findBySemester($currentSemester);

        return $this->render('school_admin/school_allocate_main.html.twig', array(
            'applications' => $applications,
            'allocations' => $allCurrentSchoolCapacities,
        ));
    }
}
