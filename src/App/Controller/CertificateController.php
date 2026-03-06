<?php

namespace App\Controller;

use App\Entity\Repository\AssistantHistoryRepository;
use App\Entity\Repository\CertificateRequestRepository;
use App\Entity\Repository\DepartmentRepository;
use App\Entity\Repository\SemesterRepository;
use App\Entity\Repository\SignatureRepository;
use App\Entity\Signature;
use App\Form\Type\CreateSignatureType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CertificateController extends BaseController
{
    public function __construct(
        private readonly AssistantHistoryRepository $assistantHistoryRepo,
        private readonly SignatureRepository $signatureRepo,
        private readonly CertificateRequestRepository $certificateRequestRepo,
        private readonly FileUploader $fileUploader,
        private readonly EntityManagerInterface $em,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    /**
     * @return RedirectResponse|Response
     */
    #[Route('/kontrollpanel/attest/{id}', name: 'certificate_show', defaults: ['id' => null], methods: ['GET', 'POST'])]
    public function showAction(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);

        $assistants = $this->assistantHistoryRepo->findByDepartmentAndSemester($department, $semester);

        $signature = $this->signatureRepo->findByUser($this->getUser());
        $oldPath = '';
        if ($signature === null) {
            $signature = new Signature();
        } else {
            $oldPath = $signature->getSignaturePath();
        }

        $form = $this->createForm(CreateSignatureType::class, $signature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $isImageUpload = $request->files->get('create_signature')['signature_path'] !== null;

            if ($isImageUpload) {
                $signaturePath = $this->fileUploader->uploadSignature($request);
                $this->fileUploader->deleteSignature($oldPath);

                $signature->setSignaturePath($signaturePath);
            } else {
                $signature->setSignaturePath($oldPath);
            }

            $signature->setUser($this->getUser());
            $this->em->persist($signature);
            $this->em->flush();

            $this->addFlash('success', 'Signatur og evt. kommentar ble lagret');

            return $this->redirect($request->headers->get('referer'));
        }

        // Finds all the the certificate requests
        $certificateRequests = $this->certificateRequestRepo->findAll();

        return $this->render('certificate/index.html.twig', [
            'certificateRequests' => $certificateRequests,
            'form' => $form->createView(),
            'signature' => $signature,
            'assistants' => $assistants,
            'department' => $department,
            'currentSemester' => $semester,
        ]);
    }
}
