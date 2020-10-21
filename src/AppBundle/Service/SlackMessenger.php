<?php

namespace AppBundle\Service;

use AppBundle\Entity\Department;
use Exception;
use Monolog\Logger;
use Nexy\Slack\Attachment;
use Nexy\Slack\Client;
use Nexy\Slack\Message;

class SlackMessenger
{
    private $slackClient;
    private $notificationChannel;
    private $logChannel;
    private $logger;
    private $disableDelivery;

    /**
     * SlackMessenger constructor.
     *
     * @param Client $slackClient
     * @param string $notificationChannel
     * @param string $logChannel
     * @param bool   $disableDelivery
     * @param Logger $logger
     */
    public function __construct(Client $slackClient, string $notificationChannel, string $logChannel, bool $disableDelivery, Logger $logger)
    {
        $this->slackClient = $slackClient;
        $this->notificationChannel = $notificationChannel;
        $this->logChannel = $logChannel;
        $this->logger = $logger;
        $this->disableDelivery = $disableDelivery;
    }

    public function notify(string $messageBody)
    {
        $message = $this->slackClient->createMessage();

        $message
            ->to($this->notificationChannel)
            ->setText($messageBody);

        $this->send($message);
    }

    public function log(string $messageBody, array $attachmentData = [])
    {
        $message = $this->slackClient->createMessage();
        $message->to($this->logChannel);
        $attachment = $this->createAttachment($attachmentData);

        if (empty($attachmentData) || $attachment === null) {
            $message->setText($messageBody);
        } else {
            $message->setAttachments([$attachment]);
        }

        $this->send($message);
    }

    public function messageDepartment(string $messageBody, Department $department)
    {
        if (!$department->getSlackChannel()) {
            return;
        }

        $message = $this->slackClient->createMessage();

        $message
            ->to($department->getSlackChannel())
            ->setText($messageBody);

        $this->send($message);
    }
    
    public function send(Message $message)
    {
        if ($message->getChannel() === null) {
            $message->setChannel($this->logChannel);
        }

        if (!$this->disableDelivery) {
            try {
                $this->slackClient->sendMessage($message);
            } catch (Exception $e) {
                $this->logger->error("Sending message to Slack failed! {$e->getMessage()}");
            }
        }

        $this->logger->info("Slack message sent to {$message->getChannel()}: {$message->getText()}");
    }
    
    public function createMessage(): Message
    {
        return $this->slackClient->createMessage();
    }

    private function createAttachment(array $data)
    {
        $attachment = new Attachment();
        $hasData = false;

        if (isset($data['color'])) {
            $attachment->setColor($data['color']);
            $hasData = true;
        }

        if (isset($data['author_name'])) {
            $attachment->setAuthorName($data['author_name']);
            $hasData = true;
        }

        if (isset($data['author_icon'])) {
            $attachment->setAuthorIcon($data['author_icon']);
            $hasData = true;
        }

        if (isset($data['text'])) {
            $attachment->setText($data['text']);
            $hasData = true;
        }

        if (isset($data['footer'])) {
            $attachment->setFooter($data['footer']);
            $hasData = true;
        }

        if (!$hasData) {
            return null;
        }

        return $attachment;
    }
}
