<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AssistantHistory;
use AppBundle\Entity\CertificateRequest;
use AppBundle\Entity\Signature;
use AppBundle\Form\Type\CreateSignatureType;
use AppBundle\Repository\Contract\AssistantHistoryRepositoryInterface;
use AppBundle\Service\Contract\FileUploaderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CertificateController extends BaseController
{
    private $assistantHistoryRepository;
    private $entityManager;
    private $fileUploader;

    /**
     * @param AssistantHistoryRepositoryInterface $assistantHistoryRepository
     * @param EntityManagerInterface $entityManager
     * @param FileUploaderInterface $fileUploader
     */
    public function __construct(
        AssistantHistoryRepositoryInterface $assistantHistoryRepository,
        EntityManagerInterface $entityManager,
        FileUploaderInterface $fileUploader
    ) {
        $this->assistantHistoryRepository = $assistantHistoryRepository;
        $this->entityManager = $entityManager;
        $this->fileUploader = $fileUploader;
    }
    /**
     * @Route(
     *     "/kontrollpanel/attest/{id}",
     *     name="certificate_show",
     *     defaults={"id": null},
     *     methods={"GET", "POST"}
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function showAction(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);

        $assistants = $this->assistantHistoryRepository->findByDepartmentAndSemester($department, $semester);

        $signature = $this->entityManager->getRepository(Signature::class)->findByUser($this->getUser());
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
            $this->entityManager->persist($signature);
            $this->entityManager->flush();

            $this->addFlash('success', 'Signatur og evt. kommentar ble lagret');
            return $this->redirect($request->headers->get('referer'));
        }

        // Finds all the the certificate requests
        $certificateRequests = $this->entityManager->getRepository(CertificateRequest::class)->findAll();

        return $this->render('certificate/index.html.twig', array(
            'certificateRequests' => $certificateRequests,
            'form' => $form->createView(),
            'signature' => $signature,
            'assistants' => $assistants,
            'department' => $department,
            'currentSemester' => $semester,
        ));
    }
}
