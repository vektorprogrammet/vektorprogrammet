<?php

declare(strict_types=1);

namespace App\Operations\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Identity\Infrastructure\Entity\User;
use App\Operations\Api\Resource\AdminReceiptListResource;
use App\Operations\Infrastructure\Repository\ReceiptRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AdminReceiptListProvider implements ProviderInterface
{
    public function __construct(
        private readonly ReceiptRepository $receiptRepository,
        private readonly Security $security,
    ) {
    }

    /**
     * @return AdminReceiptListResource[]
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            throw new AccessDeniedHttpException();
        }

        $department = $user->getDepartment();
        if ($department === null) {
            return [];
        }

        $filters = $context['filters'] ?? [];
        $status = $filters['status'] ?? null;

        $receipts = $this->receiptRepository->findByDepartment($department, $status);

        $resources = [];
        foreach ($receipts as $receipt) {
            $resource = new AdminReceiptListResource();
            $resource->id = $receipt->getId();
            $resource->visualId = $receipt->getVisualId();
            $resource->userName = $receipt->getUser()?->getFullName();
            $resource->description = $receipt->getDescription();
            $resource->sum = $receipt->getSum();
            $resource->receiptDate = $receipt->getReceiptDate()?->format('Y-m-d');
            $resource->submitDate = $receipt->getSubmitDate()?->format('Y-m-d');
            $resource->status = $receipt->getStatus();
            $resources[] = $resource;
        }

        return $resources;
    }
}
