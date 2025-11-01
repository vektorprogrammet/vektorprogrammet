<?php

namespace App\Contract;

use App\Models\AdmissionSubscriber;
use App\Models\Receipt;
use App\Models\SupportTicket;

/**
 * Interface for EmailSender service.
 * Defines contract for email sending operations.
 */
interface EmailSenderInterface
{
    /**
     * Send support ticket to department.
     *
     * @param SupportTicket $supportTicket
     */
    public function sendSupportTicketToDepartment(SupportTicket $supportTicket);

    /**
     * Send support ticket receipt.
     *
     * @param SupportTicket $supportTicket
     */
    public function sendSupportTicketReceipt(SupportTicket $supportTicket);

    /**
     * Send paid receipt confirmation.
     *
     * @param Receipt $receipt
     */
    public function sendPaidReceiptConfirmation(Receipt $receipt);

    /**
     * Send rejected receipt confirmation.
     *
     * @param Receipt $receipt
     */
    public function sendRejectedReceiptConfirmation(Receipt $receipt);

    /**
     * Send receipt created notification.
     *
     * @param Receipt $receipt
     */
    public function sendReceiptCreatedNotification(Receipt $receipt);

    /**
     * Send admission started notification.
     *
     * @param AdmissionSubscriber $subscriber
     */
    public function sendAdmissionStartedNotification(AdmissionSubscriber $subscriber);

    /**
     * Send info meeting notification.
     *
     * @param AdmissionSubscriber $subscriber
     */
    public function sendInfoMeetingNotification(AdmissionSubscriber $subscriber);
}
