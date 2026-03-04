<?php

namespace App\Controller;

use App\Entity\Repository\DepartmentRepository;
use App\Entity\Repository\SemesterRepository;
use Symfony\Component\Routing\Attribute\Route;

class TeacherController extends BaseController
{
    public function __construct(
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    #[Route('/skoler', name: 'schools', methods: ['GET'])]
    #[Route('/laerere', name: 'teachers')]
    public function indexAction()
    {
        return $this->render('teacher/index.html.twig');
    }
}
