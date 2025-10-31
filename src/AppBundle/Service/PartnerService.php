<?php

namespace AppBundle\Service;

use AppBundle\Entity\AssistantHistory;
use AppBundle\Entity\User;
use AppBundle\Repository\Contract\AssistantHistoryRepositoryInterface;
use AppBundle\Service\Contract\PartnerServiceInterface;

/**
 * Service for finding partners for users.
 * Extracts business logic from UserController::myPartnerAction().
 */
class PartnerService implements PartnerServiceInterface
{
    private $assistantHistoryRepository;

    /**
     * @param AssistantHistoryRepositoryInterface $assistantHistoryRepository
     */
    public function __construct(AssistantHistoryRepositoryInterface $assistantHistoryRepository)
    {
        $this->assistantHistoryRepository = $assistantHistoryRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function findPartnersForUser(User $user): array
    {
        $activeAssistantHistories = $this->assistantHistoryRepository->findActiveAssistantHistoriesByUser($user);
        
        $partnerInformations = [];
        $partnerCount = 0;

        foreach ($activeAssistantHistories as $activeHistory) {
            $schoolHistories = $this->assistantHistoryRepository->findActiveAssistantHistoriesBySchool(
                $activeHistory->getSchool()
            );
            $partners = [];

            foreach ($schoolHistories as $sh) {
                if ($sh->getUser() === $user) {
                    continue;
                }
                if ($sh->getDay() !== $activeHistory->getDay()) {
                    continue;
                }
                if ($activeHistory->activeInGroup(1) && $sh->activeInGroup(1) ||
                    $activeHistory->activeInGroup(2) && $sh->activeInGroup(2)) {
                    $partners[] = $sh;
                    $partnerCount++;
                }
            }
            $partnerInformations[] = [
                'school' => $activeHistory->getSchool(),
                'assistantHistory' => $activeHistory,
                'partners' => $partners,
            ];
        }

        return [
            'partnerInformations' => $partnerInformations,
            'partnerCount' => $partnerCount,
        ];
    }
}

