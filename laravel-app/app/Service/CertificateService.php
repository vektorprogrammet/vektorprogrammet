<?php

namespace App\Service;

use App\Models\AssistantHistory;
use App\Models\CertificateRequest;
use App\Models\Department;
use App\Models\Semester;
use App\Models\Signature;
use App\Models\User;
use App\Repository\Contract\AssistantHistoryRepositoryInterface;
use App\Service\Contract\CertificateServiceInterface;
use App\Service\Contract\FileUploaderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Service for certificate-related business logic.
 */
class CertificateService implements CertificateServiceInterface
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
     * {@inheritdoc}
     */
    public function getOrCreateSignature(User $user): Signature
    {
        $signature = $this->entityManager->getRepository(Signature::class)->findByUser($user);

        if ($signature === null) {
            $signature = new Signature();
        }

        return $signature;
    }

    /**
     * {@inheritdoc}
     */
    public function saveSignature(Signature $signature, Request $request, User $user): void
    {
        $oldPath = $signature->getSignaturePath() ?? '';
        $isImageUpload = $request->files->get('create_signature')['signature_path'] !== null;

        if ($isImageUpload) {
            $signaturePath = $this->fileUploader->uploadSignature($request);
            $this->fileUploader->deleteSignature($oldPath);
            $signature->setSignaturePath($signaturePath);
        } else {
            $signature->setSignaturePath($oldPath);
        }

        $signature->setUser($user);
        $this->entityManager->persist($signature);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getAssistantsByDepartmentAndSemester(Department $department, Semester $semester): array
    {
        return $this->assistantHistoryRepository->findByDepartmentAndSemester($department, $semester);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllCertificateRequests(): array
    {
        return $this->entityManager->getRepository(CertificateRequest::class)->findAll();
    }
}

