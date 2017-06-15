<?php

namespace AppBundle\Event;

use AppBundle\Entity\Receipt;
use Symfony\Component\EventDispatcher\Event;

class ReceiptCreatedEvent extends Event
{
    const NAME = 'receipt.created';

    private $receipt;

    /**
     * ReceiptCreatedEvent constructor.
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
