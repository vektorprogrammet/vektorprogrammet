<?php


namespace AppBundle\Utils;

use AppBundle\Entity\Receipt;
use DateTime;

class ReceiptStatistics
{
    private $receipts;
    private $refundDateImplementationDate;

    /**
     * @param $receipts []Receipt
     */
    public function __construct($receipts)
    {
        $this->receipts = $receipts;
        $this->refundDateImplementationDate = new DateTime('2018-02-16');
    }

    /**
     * @param string $year
     *
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
        $receipts = array_filter($this->receipts, function (Receipt $receipt) {
            return $receipt->getRefundDate() !== null && $receipt->getRefundDate() > $this->refundDateImplementationDate;
        });

        if (empty($receipts)) {
            return 0;
        }

        $totalHours = array_reduce($receipts, function (int $carry, Receipt $receipt) {
            $diff = $receipt->getRefundDate()->diff($receipt->getSubmitDate());
            return $carry + $diff->days * 24 + $diff->h + $diff->i/60;
        }, 0);

        return intval(round($totalHours/count($receipts)));
    }

    /**
     * @return float
     */
    public function totalAmount()
    {
        return array_reduce($this->receipts, function (float $carry, Receipt $receipt) {
            return $carry + $receipt->getSum();
        }, 0.0);
    }
}
