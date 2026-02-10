<?php

namespace App\Controller;

use App\Entity\Repository\DepartmentRepository;
use App\Entity\Repository\SemesterRepository;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelInterface;

class FrontEndController extends BaseController
{
    public function __construct(
        private KernelInterface $kernel,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    public function indexAction()
    {
        $indexFile = $this->kernel->getRootDir() . '/../client/build/index.html';
        if (!file_exists($indexFile)) {
            throw new NotFoundHttpException();
        }

        return new BinaryFileResponse($indexFile);
    }
}
