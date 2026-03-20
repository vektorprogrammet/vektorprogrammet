<?php

namespace App\Operations\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Operations\Infrastructure\Entity\Receipt;
use App\Identity\Infrastructure\Entity\User;
use App\Operations\Domain\Events\ReceiptEvent;
use App\Support\Infrastructure\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ReceiptDeleteProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly FileUploader $fileUploader,
        private readonly Security $security,
        private readonly EntityManagerInterface $em,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        /** @var User $user */
        $user = $this->security->getUser();

        $id = $uriVariables['id'] ?? null;
        $receipt = $this->em->getRepository(Receipt::class)->find($id);

        if (!$receipt) {
            throw new NotFoundHttpException('Receipt not found.');
        }

        $isOwnerAndPending = $receipt->getUser() === $user && $receipt->getStatus() === Receipt::STATUS_PENDING;
        $isTeamLeaderOrAbove = $this->security->isGranted('ROLE_TEAM_LEADER');

        if (!$isOwnerAndPending && !$isTeamLeaderOrAbove) {
            throw new AccessDeniedHttpException('You can only delete your own pending receipts, or be a team leader.');
        }

        // Delete associated file
        $picturePath = $receipt->getPicturePath();
        if ($picturePath) {
            $this->fileUploader->deleteReceipt($picturePath);
        }

        $this->em->remove($receipt);
        $this->em->flush();

        try {
            $this->eventDispatcher->dispatch(new ReceiptEvent($receipt), ReceiptEvent::DELETED);
        } catch (\Throwable $e) {
            $this->logger->error('Failed to dispatch receipt deleted event: '.$e->getMessage());
        }
    }
}
