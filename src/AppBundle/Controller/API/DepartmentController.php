<?php

namespace AppBundle\Controller\API;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use AppBundle\Entity\Semester;
use AppBundle\Entity\Department;

class DepartmentController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Gets an individual Department
     *
     * @param int $id
     * @return mixed
     *
     * @ApiDoc(
     *     section="Department",
     *     requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Department ID"
     *      }
     *     },
     *     output="AppBundle\Entity\Department",
     *     statusCodes={
     *          200 = "Returned when successful",
     *          404 = "Return when not found",
     *          500 = "Internal server error"
     *     }
     * )
     */
    public function getAction(int $id)
    {
        return $this->getDoctrine()->getRepository('AppBundle:Department')->find($id);
    }

    /**
     * Get department by shortName
     *
     * @param string $shortName
     * @return mixed
     *
     * @ApiDoc(
     *     section="Department",
     *     statusCodes={
     *          200 = "Returned when successful",
     *          404 = "Return when not found",
     *          500 = "Internal server error"
     *     }
     * )
     */
    public function getShortnameAction($shortName)
    {
        return $this->getDoctrine()->getRepository('AppBundle:Department')->findDepartmentByShortName($shortName);
    }

    /**
     * Gets all Departments
     *
     * @return mixed
     *
     * @ApiDoc(
     *     section="Department",
     *     statusCodes={
     *          200 = "Returned when successful",
     *          404 = "Return when not found",
     *          500 = "Internal server error"
     *     }
     * )
     */
    public function cgetAction()
    {
        return $this->getDoctrine()->getRepository('AppBundle:Department')->findAll();
    }

    /**
     * Gets Active Departments by Semester
     *
     * @return mixed
     *
     * @ApiDoc(
     *     section="Department",
     *     statusCodes={
     *          200 = "Returned when successful",
     *          404 = "Return when not found",
     *          500 = "Internal server error"
     *     }
     * )
     */

    public function activeAdmissionAction()
    {
        $activeSemesters = $this->getDoctrine()->getRepository('AppBundle:Semester')->findSemestersWithActiveAdmission();
        $activeDepartments = $this->getDepartmentsFromSemesters($activeSemesters);

        return $activeDepartments;
    }

    /**
     * @param Semester[] $semesters
     *
     * @return Department[]
     */
    private function getDepartmentsFromSemesters($semesters)
    {
        $departments = [];
        foreach ($semesters as $semester) {
            $department = $semester->getDepartment();
            if (!in_array($department, $departments)) {
                $departments[] = $department;
            }
        }

        return $departments;
    }
}
