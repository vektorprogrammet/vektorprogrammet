<?php

declare(strict_types=1);

namespace App\Operations\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Identity\Infrastructure\AccessControlService;
use App\Identity\Infrastructure\Entity\User;
use App\Operations\Infrastructure\Entity\Receipt;
use App\Operations\Infrastructure\Repository\ReceiptRepository;
use App\Operations\Domain\Events\ReceiptEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class AdminReceiptStatusProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly ReceiptRepository $receiptRepository,
        private readonly EntityManagerInterface $em,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly AccessControlService $accessControlService,
        private readonly Security $security,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $id = $uriVariables['id'] ?? null;
        $receipt = $id !== null ? $this->receiptRepository->find($id) : null;

        if ($receipt === null) {
            throw new NotFoundHttpException('Receipt not found.');
        }

        $currentUser = $this->security->getUser();
        $receiptDepartment = $receipt->getUser()?->getDepartment();
        if ($receiptDepartment === null) {
            if (!$this->security->isGranted('ROLE_ADMIN')) {
                throw new AccessDeniedHttpException('Receipt owner has no department.');
            }
        } elseif ($currentUser instanceof User) {
            $this->accessControlService->assertDepartmentAccess($receiptDepartment, $currentUser);
        }

        $status = $data->status;

        try {
            $receipt->setStatus($status);
        } catch (\InvalidArgumentException $e) {
            throw new UnprocessableEntityHttpException($e->getMessage(), $e);
        }

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
