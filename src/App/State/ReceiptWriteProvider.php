<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\ReceiptWriteResource;
use App\Entity\Receipt;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Provides a ReceiptWriteResource for PUT and DELETE operations.
 * Loads the receipt entity and returns a DTO representation.
 */
class ReceiptWriteProvider implements ProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ReceiptWriteResource
    {
        $id = $uriVariables['id'] ?? null;
        $receipt = $this->em->getRepository(Receipt::class)->find($id);

        if (!$receipt) {
            throw new NotFoundHttpException('Receipt not found.');
        }

        $resource = new ReceiptWriteResource();
        $resource->id = $receipt->getId();
        $resource->description = $receipt->getDescription();
        $resource->sum = $receipt->getSum();
        $resource->receiptDate = $receipt->getReceiptDate()?->format('Y-m-d');

        return $resource;
    }
}
