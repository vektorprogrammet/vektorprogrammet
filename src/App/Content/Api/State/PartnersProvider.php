<?php

namespace App\Content\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Content\Api\Resource\PartnersResource;
use App\Operations\Infrastructure\Repository\AssistantHistoryRepository;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;

class PartnersProvider implements ProviderInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly AssistantHistoryRepository $assistantHistoryRepo,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): PartnersResource
    {
        /** @var User $user */
        $user = $this->security->getUser();

        $resource = new PartnersResource();

        $activeHistories = $this->assistantHistoryRepo->findActiveAssistantHistoriesByUser($user);
        $seen = [];

        foreach ($activeHistories as $activeHistory) {
            $schoolHistories = $this->assistantHistoryRepo
                ->findActiveAssistantHistoriesBySchool($activeHistory->getSchool());

            foreach ($schoolHistories as $sh) {
                $partner = $sh->getUser();
                if ($partner === $user) {
                    continue;
                }

                $partnerId = $partner->getId();
                if (isset($seen[$partnerId])) {
                    continue;
                }
                $seen[$partnerId] = true;

                $resource->partners[] = [
                    'firstName' => $partner->getFirstName(),
                    'lastName' => $partner->getLastName(),
                    'phone' => $partner->getPhone(),
                    'email' => $partner->getEmail(),
                ];
            }
        }

        return $resource;
    }
}
