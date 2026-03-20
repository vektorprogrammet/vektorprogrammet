<?php

namespace App\Controller;

use App\Support\Controller\BaseController;
use App\Entity\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ConfirmationController extends BaseController
{
    public function __construct(
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    /**
     * @return Response
     */
    #[Route('/bekreftelse', name: 'confirmation', methods: ['GET'])]
    public function showAction()
    {
        return $this->render('confirmation/confirmation.html.twig');
    }
}
