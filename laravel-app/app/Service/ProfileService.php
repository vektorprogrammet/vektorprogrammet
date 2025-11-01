<?php

namespace App\Service;

use App\Models\AssistantHistory;
use App\Models\ExecutiveBoardMembership;
use App\Models\Signature;
use App\Models\TeamMembership;
use App\Models\User;
use App\Repository\Contract\AssistantHistoryRepositoryInterface;
use App\Repository\Contract\ExecutiveBoardMembershipRepositoryInterface;
use App\Repository\Contract\SignatureRepositoryInterface;
use App\Repository\Contract\TeamMembershipRepositoryInterface;
use App\Service\Contract\ProfileServiceInterface;
use App\Service\Contract\RoleManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Service for profile operations.
 * Extracts business logic from ProfileController.
 */
class ProfileService implements ProfileServiceInterface
{
    private AssistantHistoryRepositoryInterface $assistantHistoryRepository;
    private TeamMembershipRepositoryInterface $teamMembershipRepository;
    private ExecutiveBoardMembershipRepositoryInterface $executiveBoardMembershipRepository;
    private SignatureRepositoryInterface $signatureRepository;
    private RoleManagerInterface $roleManager;

    /**
     * @param AssistantHistoryRepositoryInterface $assistantHistoryRepository
     * @param TeamMembershipRepositoryInterface $teamMembershipRepository
     * @param ExecutiveBoardMembershipRepositoryInterface $executiveBoardMembershipRepository
     * @param SignatureRepositoryInterface $signatureRepository
     * @param RoleManagerInterface $roleManager
     */
    public function __construct(
        AssistantHistoryRepositoryInterface $assistantHistoryRepository,
        TeamMembershipRepositoryInterface $teamMembershipRepository,
        ExecutiveBoardMembershipRepositoryInterface $executiveBoardMembershipRepository,
        SignatureRepositoryInterface $signatureRepository,
        RoleManagerInterface $roleManager
    ) {
        $this->assistantHistoryRepository = $assistantHistoryRepository;
        $this->teamMembershipRepository = $teamMembershipRepository;
        $this->executiveBoardMembershipRepository = $executiveBoardMembershipRepository;
        $this->signatureRepository = $signatureRepository;
        $this->roleManager = $roleManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getProfileData(User $user): array
    {
        $assistantHistory = $this->assistantHistoryRepository->findByUser($user);
        $teamMemberships = $this->teamMembershipRepository->findByUser($user);
        $executiveBoardMemberships = $this->executiveBoardMembershipRepository->findByUser($user);

        return [
            'user' => $user,
            'assistantHistory' => $assistantHistory,
            'teamMemberships' => $teamMemberships,
            'executiveBoardMemberships' => $executiveBoardMemberships,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function canViewProfile(User $viewedUser, ?User $viewingUser, bool $isGrantedAssistant): bool
    {
        $teamMemberships = $this->teamMembershipRepository->findByUser($viewedUser);
        $executiveBoardMemberships = $this->executiveBoardMembershipRepository->findByUser($viewedUser);

        return !empty($teamMemberships) || !empty($executiveBoardMemberships) || $isGrantedAssistant;
    }

    /**
     * {@inheritdoc}
     */
    public function activateUser(User $user): void
    {
        $user->is_active = true;
        $user->save();
    }

    /**
     * {@inheritdoc}
     */
    public function deactivateUser(User $user): void
    {
        $user->is_active = false;
        $user->save();
    }

    /**
     * {@inheritdoc}
     */
    public function getCertificateData(User $user, User $signer, string $projectDir): array
    {
        $assistantHistory = $this->assistantHistoryRepository->findByUser($user);
        $teamMembership = $this->teamMembershipRepository->findByUser($user);
        $signature = $this->signatureRepository->findByUser($signer);

        if ($signature === null) {
            throw new \RuntimeException('Signature not found');
        }

        $department = $signer->getDepartment();
        $additionalComment = $signature->additional_comment ?? null;

        return [
            'user' => $user,
            'assistantHistory' => $assistantHistory,
            'teamMembership' => $teamMembership,
            'signature' => $signature,
            'additional_comment' => $additionalComment,
            'department' => $department,
            'base_dir' => $projectDir . '/web',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function generateCertificatePdf(string $html, string $filename = 'attest.pdf'): void
    {
        $options = new Options();
        $options->setIsRemoteEnabled(true);
        $options->setChroot("/../");

        $dompdf = new Dompdf($options);
        $dompdf->setPaper('A4');

        // Remove extra whitespace between tags
        $html = preg_replace('/>\s+</', "><", $html);
        $dompdf->loadHtml($html);

        $dompdf->render();

        $dompdf->stream($filename);
    }
}

