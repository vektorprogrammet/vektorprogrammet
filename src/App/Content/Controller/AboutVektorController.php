<?php

namespace App\Content\Controller;

use App\Support\Controller\BaseController;
use App\Organization\Infrastructure\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use Symfony\Component\Routing\Attribute\Route;

class AboutVektorController extends BaseController
{
    public function __construct(
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    #[Route('/bedrifter', name: 'businesses', methods: ['GET'])]
    #[Route('/omvektor', name: 'about', methods: ['GET'])]
    #[Route('/faq', name: 'faq', methods: ['GET'])]
    #[Route('/om', name: 'about_new')]
    public function showAction()
    {
        return $this->render('about/about_vektor.html.twig');
    }
}
