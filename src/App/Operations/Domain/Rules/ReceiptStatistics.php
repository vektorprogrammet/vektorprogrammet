<?php

namespace App\Operations\Domain\Rules;

use App\Operations\Infrastructure\Entity\Receipt;

class ReceiptStatistics
{
    private $refundDateImplementationDate;

    /**
     * @param $receipts []Receipt
     */
    public function __construct(private $receipts)
    {
        $this->refundDateImplementationDate = new \DateTime('2018-02-16');
    }

    /**
     * @return float
     */
    public function totalPayoutIn(string $year)
    {
        return array_reduce($this->receipts, function (int $carry, Receipt $receipt) use ($year) {
            if (!$receipt->getRefundDate() || $receipt->getRefundDate()->format('Y') !== $year) {
                return $carry;
            }

            return $carry + $receipt->getSum();
        }, 0.0);
    }

    /**
     * @return int
     */
    public function averageRefundTimeInHours()
    {
        $receipts = array_filter($this->receipts, fn (Receipt $receipt) => $receipt->getRefundDate() !== null && $receipt->getRefundDate() > $this->refundDateImplementationDate);

        if (empty($receipts)) {
            return 0;
        }

        $totalHours = array_reduce($receipts, function (int $carry, Receipt $receipt) {
            $diff = $receipt->getRefundDate()->diff($receipt->getSubmitDate());

            return $carry + $diff->days * 24 + $diff->h + $diff->i / 60;
        }, 0);

        return intval(round($totalHours / count($receipts)));
    }

    /**
     * @return float
     */
    public function totalAmount()
    {
        return array_reduce($this->receipts, fn (float $carry, Receipt $receipt) => $carry + $receipt->getSum(), 0.0);
    }
}
