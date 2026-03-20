<?php

namespace App\Controller;

use App\Entity\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use Symfony\Component\Routing\Attribute\Route;

class ParentsController extends BaseController
{
    public function __construct(
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    #[Route('/foreldre', name: 'parents', methods: ['GET'])]
    public function indexAction()
    {
        return $this->render('/parents/parents.html.twig');
    }
}
