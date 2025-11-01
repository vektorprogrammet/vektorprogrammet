<?php

namespace AppBundle\Service;

use AppBundle\Entity\AssistantHistory;
use AppBundle\Entity\ExecutiveBoardMembership;
use AppBundle\Entity\Signature;
use AppBundle\Entity\TeamMembership;
use AppBundle\Entity\User;
use AppBundle\Repository\Contract\AssistantHistoryRepositoryInterface;
use AppBundle\Repository\Contract\ExecutiveBoardMembershipRepositoryInterface;
use AppBundle\Repository\Contract\TeamMembershipRepositoryInterface;
use AppBundle\Service\Contract\ProfileServiceInterface;
use AppBundle\Service\Contract\RoleManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Service for profile operations.
 * Extracts business logic from ProfileController.
 */
class ProfileService implements ProfileServiceInterface
{
    private $assistantHistoryRepository;
    private $teamMembershipRepository;
    private $executiveBoardMembershipRepository;
    private $roleManager;
    private $entityManager;

    /**
     * @param AssistantHistoryRepositoryInterface $assistantHistoryRepository
     * @param TeamMembershipRepositoryInterface $teamMembershipRepository
     * @param ExecutiveBoardMembershipRepositoryInterface $executiveBoardMembershipRepository
     * @param RoleManagerInterface $roleManager
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        AssistantHistoryRepositoryInterface $assistantHistoryRepository,
        TeamMembershipRepositoryInterface $teamMembershipRepository,
        ExecutiveBoardMembershipRepositoryInterface $executiveBoardMembershipRepository,
        RoleManagerInterface $roleManager,
        EntityManagerInterface $entityManager
    ) {
        $this->assistantHistoryRepository = $assistantHistoryRepository;
        $this->teamMembershipRepository = $teamMembershipRepository;
        $this->executiveBoardMembershipRepository = $executiveBoardMembershipRepository;
        $this->roleManager = $roleManager;
        $this->entityManager = $entityManager;
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
        $user->setActive(true);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function deactivateUser(User $user): void
    {
        $user->setActive(false);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getCertificateData(User $user, User $signer, string $projectDir): array
    {
        $assistantHistory = $this->assistantHistoryRepository->findByUser($user);
        $teamMembership = $this->teamMembershipRepository->findByUser($user);
        $signatureRepo = $this->entityManager->getRepository(Signature::class);
        $signature = $signatureRepo->findByUser($signer);

        if ($signature === null) {
            throw new \RuntimeException('Signature not found');
        }

        $department = $signer->getDepartment();
        $additionalComment = $signature->getAdditionalComment();

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

