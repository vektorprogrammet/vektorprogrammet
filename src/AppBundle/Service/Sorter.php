<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;
use AppBundle\Entity\Receipt;
use Doctrine\Common\Collections\ArrayCollection;

class Sorter
{
    /**
     * @param User $user1
     * @param User $user2
     *
     * @return int
     */
    protected function userWithNewestReceipt($user1, $user2)
    {
        $user1Receipts = $user1->getReceipts();
        $user2Receipts = $user2->getReceipts();

        $this->sortReceiptsBySubmitTime($user1Receipts);
        $this->sortReceiptsBySubmitTime($user2Receipts);

        return $this->newestReceipt($user1Receipts[0], $user2Receipts[0]);
    }

    /**
     * @param Receipt $receipt1
     * @param Receipt $receipt2
     *
     * @return int
     */
    public function newestReceipt($receipt1, $receipt2)
    {
        if ($receipt1->getSubmitDate() === $receipt2->getSubmitDate()) {
            return 0;
        }

        return ($receipt1->getSubmitDate() > $receipt2->getSubmitDate()) ? -1 : 1;
    }

    /**
     * @param User[] $users
     *
     * @return bool success
     */
    public function sortUsersByReceiptSubmitTime(&$users)
    {
        return usort($users, array($this, 'userWithNewestReceipt'));
    }

    /**
     * @param User[] $users
     */
    public function sortUsersByReceiptStatus(&$users)
    {
        $usersWithPendingReceipts = [];
        $usersWithoutPendingReceipts = [];
        foreach ($users as $user) {
            if ($user->hasPendingReceipts()) {
                array_push($usersWithPendingReceipts, $user);
            } else {
                array_push($usersWithoutPendingReceipts, $user);
            }
        }

        $users = array_merge($usersWithPendingReceipts, $usersWithoutPendingReceipts);
    }

    /**
     * @param ArrayCollection $receipts
     */
    public function sortReceiptsBySubmitTime(&$receipts)
    {
        $iterator = $receipts->getIterator();
        $iterator->uasort(array($this,'newestReceipt'));
        $receipts = new ArrayCollection(iterator_to_array($iterator));
    }

    /**
     * @param ArrayCollection $receipts
     */
    public function sortReceiptsByStatus(&$receipts)
    {
        $pendingReceipts = [];
        $nonPendingReceipts = [];
        foreach ($receipts as $receipt) {
            if ($receipt->getStatus() === Receipt::STATUS_PENDING) {
                array_push($pendingReceipts, $receipt);
            } else {
                array_push($nonPendingReceipts, $receipt);
            }
        }
        $receiptElements = array_merge($pendingReceipts, $nonPendingReceipts);
        $receipts = new ArrayCollection($receiptElements);
    }
}
