<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Receipt;
use App\Entity\Repository\ReceiptRepository;
use App\Event\ReceiptEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class AdminReceiptStatusProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly ReceiptRepository $receiptRepository,
        private readonly EntityManagerInterface $em,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $id = $uriVariables['id'] ?? null;
        $receipt = $id ? $this->receiptRepository->find($id) : null;

        if (!$receipt) {
            throw new NotFoundHttpException('Receipt not found.');
        }

        $status = $data->status;

        $receipt->setStatus($status);

        if ($status === Receipt::STATUS_REFUNDED && !$receipt->getRefundDate()) {
            $receipt->setRefundDate(new \DateTime());
        }

        $this->em->flush();

        if ($status === Receipt::STATUS_REFUNDED) {
            $this->eventDispatcher->dispatch(new ReceiptEvent($receipt), ReceiptEvent::REFUNDED);
        } elseif ($status === Receipt::STATUS_REJECTED) {
            $this->eventDispatcher->dispatch(new ReceiptEvent($receipt), ReceiptEvent::REJECTED);
        } elseif ($status === Receipt::STATUS_PENDING) {
            $this->eventDispatcher->dispatch(new ReceiptEvent($receipt), ReceiptEvent::PENDING);
        }
    }
}
