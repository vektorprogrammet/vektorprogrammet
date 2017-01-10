<?php

namespace AppBundle\Service;

use Maknz\Slack\Client;
use Maknz\Slack\Message;
use Monolog\Logger;

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

    public function log(string $messageBody)
    {
        $message = $this->slackClient->createMessage();

        $message
            ->to($this->logChannel)
            ->setText($messageBody);

        $this->send($message);
    }

    private function send(Message $message)
    {
        if (!$this->disableDelivery) {
            $this->slackClient->sendMessage($message);
        }
        $this->logger->info("Slack message sent to {$message->getChannel()}: {$message->getText()}");
    }
}
