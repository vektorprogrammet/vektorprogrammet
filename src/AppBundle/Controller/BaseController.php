<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BaseController extends Controller
{
    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return parent::getUser();
    }

    /**
     * Tries to get department from the Request and opts to the user's department if none is found.
     * Returns null if none can be found this way.
     *
     * @return Department|null
     */
    public function getDepartment(): ?Department
    {
        $department = null;
        $request = Request::createFromGlobals();
        $departmentId = $request->query->get('department');
        if ($departmentId === null) {
            if ($this->getUser() !== null) {
                $department = $this->getUser()->getDepartment();
            }
        } else {
            $department = $this->getDoctrine()->getRepository('AppBundle:Department')->find($departmentId);
        }
        return $department;
    }

    /**
     * Tries to get semester from the Request and opts to the current if none is found.
     * Returns null if the given ID has no corresponding semester.
     *
     * @return Semester|null
     */
    public function getSemester(): ?Semester
    {
        $request = Request::createFromGlobals();
        $semesterId = $request->query->get('semester');
        if ($semesterId === null) {
            $semester = $this->getDoctrine()->getRepository('AppBundle:Semester')->findCurrentSemester();
        } else {
            $semester = $this->getDoctrine()->getRepository('AppBundle:Semester')->find($semesterId);
        }
        return $semester;
    }

    /**
     * 404's if department is null in the request and for the user, or if a wrong department ID is given.
     *
     * @return Department
     */
    public function getDepartmentOrThrow404(): Department
    {
        $department = $this->getDepartment();
        if ($department === null) {
            throw new NotFoundHttpException();
        }
        return $department;
    }

    /**
     * @return Semester
     */
    public function getSemesterOrThrow404(): Semester
    {
        $semester = $this->getSemester();
        if ($semester === null) {
            throw new NotFoundHttpException();
        }
        return $semester;
    }
}
