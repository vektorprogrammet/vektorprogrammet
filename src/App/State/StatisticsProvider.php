<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\Statistics;
use App\Entity\Repository\AssistantHistoryRepository;
use App\Entity\Repository\UserRepository;

class StatisticsProvider implements ProviderInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly AssistantHistoryRepository $assistantHistoryRepository,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): Statistics
    {
        $stats = new Statistics();
        $stats->assistantCount = count($this->userRepository->findAssistants()) + 600;
        $stats->teamMemberCount = count($this->userRepository->findTeamMembers()) + 160;
        $stats->femaleAssistantCount = $this->assistantHistoryRepository->numFemale();
        $stats->maleAssistantCount = $this->assistantHistoryRepository->numMale();

        return $stats;
    }
}
