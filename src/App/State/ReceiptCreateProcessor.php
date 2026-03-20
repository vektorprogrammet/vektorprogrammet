<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\ReceiptWriteResource;
use App\Entity\Receipt;
use App\Entity\User;
use App\Event\ReceiptEvent;
use App\Support\Infrastructure\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ReceiptCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly FileUploader $fileUploader,
        private readonly Security $security,
        private readonly EntityManagerInterface $em,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
    {
        assert($data instanceof ReceiptWriteResource);

        /** @var User $user */
        $user = $this->security->getUser();

        $receipt = new Receipt();
        $receipt->setUser($user);
        $receipt->setDescription($data->description);
        $receipt->setSum($data->sum);

        if ($data->receiptDate !== null) {
            try {
                $receipt->setReceiptDate(new \DateTime($data->receiptDate));
            } catch (\Exception) {
                throw new BadRequestHttpException('Invalid receipt date format.');
            }
        }

        // Handle file upload if present
        $request = $this->requestStack->getCurrentRequest();
        if ($request && $request->files->count() > 0) {
            $path = $this->fileUploader->uploadReceipt($request);
            $receipt->setPicturePath($path);
        }

        $this->em->persist($receipt);
        $this->em->flush();

        try {
            $this->eventDispatcher->dispatch(new ReceiptEvent($receipt), ReceiptEvent::CREATED);
        } catch (\Throwable $e) {
            $this->logger->error('Failed to dispatch receipt created event: '.$e->getMessage());
        }

        return new JsonResponse(['id' => $receipt->getId()], 201);
    }
}
