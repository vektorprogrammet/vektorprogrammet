<?php

namespace App\Controller;

use App\Entity\Department;
use App\Entity\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Shared\Entity\Semester;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BaseController extends AbstractController
{
    public function __construct(
        private readonly DepartmentRepository $departmentRepo,
        private readonly SemesterRepository $semesterRepo,
    ) {
    }

    public function getDepartment(Request $request): ?Department
    {
        $department = null;
        $departmentId = $request->query->get('department');
        if ($departmentId === null) {
            if ($this->getUser() !== null) {
                $department = $this->getUser()->getDepartment();
            }
        } else {
            $department = $this->departmentRepo->find($departmentId);
        }

        return $department;
    }

    public function getSemester(Request $request): ?Semester
    {
        $semesterId = $request->query->get('semester');
        if ($semesterId === null) {
            $semester = $this->getCurrentSemester();
        } else {
            $semester = $this->semesterRepo->find($semesterId);
        }

        return $semester;
    }

    public function getCurrentSemester(): Semester
    {
        return $this->semesterRepo->findOrCreateCurrentSemester();
    }

    public function getDepartmentOrThrow404(Request $request): Department
    {
        $department = $this->getDepartment($request);
        if ($department === null) {
            throw new NotFoundHttpException();
        }

        return $department;
    }

    public function getSemesterOrThrow404(Request $request): Semester
    {
        $semester = $this->getSemester($request);
        if ($semester === null) {
            throw new NotFoundHttpException();
        }

        return $semester;
    }
}
