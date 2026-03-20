<?php

namespace App\Content\Controller;

use App\Support\Controller\BaseController;
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
