<?php

namespace App\Contract;

use App\Models\Receipt;
use App\Models\TeamMembershipInterface;
use App\Models\User;

/**
 * Interface for Sorter service.
 * Defines contract for sorting operations.
 */
interface SorterInterface
{
    /**
     * Compare two receipts by date.
     *
     * @param Receipt $receipt1
     * @param Receipt $receipt2
     * @return int
     */
    public function newestReceipt(Receipt $receipt1, Receipt $receipt2): int;

    /**
     * Sort users by receipt submit time.
     *
     * @param User[] $users
     * @return bool success
     */
    public function sortUsersByReceiptSubmitTime(&$users): bool;

    /**
     * Sort users by receipt status.
     *
     * @param User[] $users
     */
    public function sortUsersByReceiptStatus(&$users);

    /**
     * Sort receipts by submit time.
     *
     * @param Receipt[] $receipts
     * @return bool success
     */
    public function sortReceiptsBySubmitTime(&$receipts): bool;

    /**
     * Sort receipts by status.
     *
     * @param Receipt[] $receipts
     */
    public function sortReceiptsByStatus(&$receipts);

    /**
     * Sort users by active positions.
     *
     * @param User[] $users
     */
    public function sortUsersByActivePositions(&$users);

    /**
     * Sort team memberships by position.
     *
     * @param TeamMembershipInterface[] $teamMemberships
     */
    public function sortTeamMembershipsByPosition(&$teamMemberships);
}
