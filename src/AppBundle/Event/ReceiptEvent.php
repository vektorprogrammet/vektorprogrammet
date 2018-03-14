<?php

namespace AppBundle\Event;

use AppBundle\Entity\Receipt;
use Symfony\Component\EventDispatcher\Event;

class ReceiptEvent extends Event
{
    const CREATED = 'receipt.created';
    const REFUNDED = 'receipt.refunded';
    const REJECTED = 'receipt.rejected';
    const PENDING = 'receipt.pending';
    const EDITED = 'receipt.edited';
    const DELETED = 'receipt.deleted';

    private $receipt;

    /**
     * ReceiptEvent constructor.
     *
     * @param Receipt $receipt
     */
    public function __construct(Receipt $receipt)
    {
        $this->receipt = $receipt;
    }

    /**
     * @return Receipt
     */
    public function getReceipt(): Receipt
    {
        return $this->receipt;
    }
}
