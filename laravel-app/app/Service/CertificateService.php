<?php

namespace App\Service;

use App\Models\AssistantHistory;
use App\Models\CertificateRequest;
use App\Models\Department;
use App\Models\Semester;
use App\Models\Signature;
use App\Models\User;
use App\Repository\Contract\AssistantHistoryRepositoryInterface;
use App\Repository\Contract\SignatureRepositoryInterface;
use App\Service\Contract\CertificateServiceInterface;
use App\Service\Contract\FileUploaderInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Service for certificate-related business logic.
 */
class CertificateService implements CertificateServiceInterface
{
    private AssistantHistoryRepositoryInterface $assistantHistoryRepository;
    private SignatureRepositoryInterface $signatureRepository;
    private FileUploaderInterface $fileUploader;

    /**
     * @param AssistantHistoryRepositoryInterface $assistantHistoryRepository
     * @param SignatureRepositoryInterface $signatureRepository
     * @param FileUploaderInterface $fileUploader
     */
    public function __construct(
        AssistantHistoryRepositoryInterface $assistantHistoryRepository,
        SignatureRepositoryInterface $signatureRepository,
        FileUploaderInterface $fileUploader
    ) {
        $this->assistantHistoryRepository = $assistantHistoryRepository;
        $this->signatureRepository = $signatureRepository;
        $this->fileUploader = $fileUploader;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrCreateSignature(User $user): Signature
    {
        $signature = $this->signatureRepository->findByUser($user);

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
        $oldPath = $signature->signature_path ?? '';
        $isImageUpload = $request->files->get('create_signature')['signature_path'] !== null;

        if ($isImageUpload) {
            $signaturePath = $this->fileUploader->uploadSignature($request);
            $this->fileUploader->deleteSignature($oldPath);
            $signature->signature_path = $signaturePath;
        } else {
            $signature->signature_path = $oldPath;
        }

        $signature->user_id = $user->id;
        $signature->save();
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
        // Note: CertificateRequestRepository not yet created - using model directly
        return CertificateRequest::all()->toArray();
    }
}

