<?php

namespace AppBundle\Service\Contract;

use AppBundle\Entity\AssistantHistory;
use AppBundle\Entity\ExecutiveBoardMembership;
use AppBundle\Entity\Signature;
use AppBundle\Entity\TeamMembership;
use AppBundle\Entity\User;

/**
 * Interface for ProfileService.
 * Defines contract for profile operations.
 */
interface ProfileServiceInterface
{
    /**
     * Get profile data for a user.
     *
     * @param User $user
     * @return array
     */
    public function getProfileData(User $user): array;

    /**
     * Check if user can view specific profile.
     *
     * @param User $viewedUser
     * @param User|null $viewingUser
     * @param bool $isGrantedAssistant
     * @return bool
     */
    public function canViewProfile(User $viewedUser, ?User $viewingUser, bool $isGrantedAssistant): bool;

    /**
     * Activate user.
     *
     * @param User $user
     */
    public function activateUser(User $user): void;

    /**
     * Deactivate user.
     *
     * @param User $user
     */
    public function deactivateUser(User $user): void;

    /**
     * Get certificate data for user.
     *
     * @param User $user
     * @param User $signer
     * @param string $projectDir
     * @return array
     */
    public function getCertificateData(User $user, User $signer, string $projectDir): array;

    /**
     * Generate PDF from HTML content for certificate.
     *
     * @param string $html The rendered HTML content
     * @param string $filename The filename for the PDF (default: 'attest.pdf')
     * @return void Outputs PDF directly via stream
     */
    public function generateCertificatePdf(string $html, string $filename = 'attest.pdf'): void;
}


