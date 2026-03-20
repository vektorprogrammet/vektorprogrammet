<?php

namespace App\Controller;

use App\Entity\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Entity\Repository\SignatureRepository;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SignatureController extends BaseController
{
    public function __construct(
        private readonly SignatureRepository $signatureRepo,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    #[Route('/signatures/{imageName}', name: 'signature_image_show', methods: ['GET'])]
    public function showSignatureImageAction($imageName)
    {
        $user = $this->getUser();

        $signature = $this->signatureRepo->findByUser($user);
        if ($signature === null) {
            throw new NotFoundHttpException('Signature not found');
        }

        $signatureImagePath = $signature->getSignaturePath();
        $signatureFileName = substr($signatureImagePath, strrpos($signatureImagePath, '/') + 1);
        if ($imageName !== $signatureFileName) {
            // Users can only view their own signatures
            throw new AccessDeniedException();
        }

        return new BinaryFileResponse($this->container->getParameter('signature_images').'/'.$signatureFileName);
    }
}
