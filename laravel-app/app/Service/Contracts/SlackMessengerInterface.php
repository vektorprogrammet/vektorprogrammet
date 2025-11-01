<?php

namespace App\Contract;

use App\Models\Department;
use Nexy\Slack\Message;

/**
 * Interface for SlackMessenger service.
 * Defines contract for Slack messaging operations.
 */
interface SlackMessengerInterface
{
    /**
     * Send notification message.
     *
     * @param string $messageBody
     */
    public function notify(string $messageBody);

    /**
     * Log message with optional attachment data.
     *
     * @param string $messageBody
     * @param array $attachmentData
     */
    public function log(string $messageBody, array $attachmentData = []);

    /**
     * Send message to department.
     *
     * @param string $messageBody
     * @param Department $department
     */
    public function messageDepartment(string $messageBody, Department $department);

    /**
     * Send message.
     *
     * @param Message $message
     */
    public function send(Message $message);

    /**
     * Create a new message.
     *
     * @return Message
     */
    public function createMessage(): Message;
}
