<?php

declare(strict_types=1);

namespace App\Operations\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Identity\Infrastructure\Entity\User;
use App\Operations\Api\Resource\UserReceiptListResource;
use App\Operations\Infrastructure\Repository\ReceiptRepository;
use Symfony\Bundle\SecurityBundle\Security;
use App\Operations\Infrastructure\Entity\Receipt;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserReceiptListProvider implements ProviderInterface
{
    public function __construct(
        private readonly ReceiptRepository $receiptRepository,
        private readonly Security $security,
    ) {
    }

    /**
     * @return UserReceiptListResource[]
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            throw new AccessDeniedHttpException();
        }

        $filters = $context['filters'] ?? [];
        $status = $filters['status'] ?? null;

        if ($status !== null && !in_array($status, [Receipt::STATUS_PENDING, Receipt::STATUS_REFUNDED, Receipt::STATUS_REJECTED], true)) {
            throw new BadRequestHttpException(sprintf('Invalid status filter "%s"', $status));
        }

        $receipts = $this->receiptRepository->findByUserOrdered($user, $status);

        $resources = [];
        foreach ($receipts as $receipt) {
            $resource = new UserReceiptListResource();
            $resource->id = $receipt->getId();
            $resource->visualId = $receipt->getVisualId();
            $resource->description = $receipt->getDescription();
            $resource->sum = $receipt->getSum();
            $resource->receiptDate = $receipt->getReceiptDate()?->format('Y-m-d');
            $resource->submitDate = $receipt->getSubmitDate()?->format('Y-m-d');
            $resource->status = $receipt->getStatus();
            $resource->refundDate = $receipt->getRefundDate()?->format('Y-m-d');
            $resources[] = $resource;
        }

        return $resources;
    }
}
