<?php

namespace AppBundle\Service\Contract;

use AppBundle\Entity\AssistantHistory;
use AppBundle\Entity\CertificateRequest;
use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use AppBundle\Entity\Signature;
use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface for certificate-related business logic.
 */
interface CertificateServiceInterface
{
    /**
     * Get or create a signature for a user.
     *
     * @param User $user The user to get/create signature for
     * @return Signature The signature entity (new or existing)
     */
    public function getOrCreateSignature(User $user): Signature;

    /**
     * Handle signature form submission - upload signature image if provided and save.
     *
     * @param Signature $signature The signature entity to update
     * @param Request $request The request containing form data
     * @param User $user The user who owns the signature
     * @return void
     */
    public function saveSignature(Signature $signature, Request $request, User $user): void;

    /**
     * Get assistants by department and semester.
     *
     * @param Department $department The department
     * @param Semester $semester The semester
     * @return AssistantHistory[] Array of assistant history entities
     */
    public function getAssistantsByDepartmentAndSemester(Department $department, Semester $semester): array;

    /**
     * Get all certificate requests.
     *
     * @return CertificateRequest[] Array of certificate request entities
     */
    public function getAllCertificateRequests(): array;
}

