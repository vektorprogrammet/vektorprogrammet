<?php

namespace App\Operations\Domain\Events;

use App\Operations\Infrastructure\Entity\Receipt;
use Symfony\Contracts\EventDispatcher\Event;

class ReceiptEvent extends Event
{
    public const CREATED = 'receipt.created';
    public const REFUNDED = 'receipt.refunded';
    public const REJECTED = 'receipt.rejected';
    public const PENDING = 'receipt.pending';
    public const EDITED = 'receipt.edited';
    public const DELETED = 'receipt.deleted';

    /**
     * ReceiptEvent constructor.
     */
    public function __construct(private readonly Receipt $receipt)
    {
    }

    public function getReceipt(): Receipt
    {
        return $this->receipt;
    }
}
