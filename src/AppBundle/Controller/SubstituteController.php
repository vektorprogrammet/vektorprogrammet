<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Semester;
use AppBundle\Role\Roles;
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

            if($semester === null) {
                $semester = $this->getDoctrine()->getRepository('AppBundle:Semester')->findLatestSemesterByDepartmentId($department->getId());
            }
        }

        $substitutes = $this->getDoctrine()->getRepository('AppBundle:Substitute')->findSubstitutesByDepartmentAndSemester($department, $semester);
        $semesters = $this->getDoctrine()->getRepository('AppBundle:Semester')->findAllSemestersByDepartment($department);

        return $this->render('substitute/index.html.twig', array(
        'substitutes' => $substitutes,
            'department' => $department,
            'semesters' => $semesters,
            'semester' => $semester,
        ));
    }

    public function deleteSubstituteByIdAction(Substitute $substitute)
    {
        // If Non-ROLE_HIGHEST_ADMIN try to delete user in other department
        if (!$this->isGranted(Roles::TEAM_LEADER) && $substitute->user->getDepartment() !== $this->getUser()->getDepartment) {
            throw new BadRequestHttpException();
        }
        try {
            // This deletes the given substitute
            $em = $this->getDoctrine()->getManager();
            $em->remove($substitute);
            $em->flush();

            return new JsonResponse(array(
                'success' => true,
            ));
        } catch (\Exception $e) {
            // Send a response back to AJAX
            return new JsonResponse([
            'success' => false,
            'code' => $e->getCode(),
            'cause' => 'Det er ikke mulig å slette vikaren. Vennligst kontakt IT-ansvarlig.',
            ]);
        }
    }

    /*public function deleteSubstituteByIdAction(Substitute $substitute)
    {
        // If Non-ROLE_HIGHEST_ADMIN try to delete user in other department
        if (!$this->isGranted(Roles::TEAM_LEADER) && $substitute->user->getDepartment() !== $this->getUser()->getDepartment) {
            throw new BadRequestHttpException();
        }
        try {
            // This deletes the given user
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();

            return new JsonResponse(array(
                'success' => true,
            ));
        } catch (\Exception $e) {
            // Send a response back to AJAX
            return new JsonResponse([
                'success' => false,
                'code' => $e->getCode(),
                'cause' => 'Det er ikke mulig å slette brukeren. Vennligst kontakt IT-ansvarlig.',
            ]);
        }
    }*/
}
