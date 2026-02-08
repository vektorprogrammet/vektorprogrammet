<?php

namespace AppBundle\Service;

use AppBundle\Entity\Department;
use Exception;
use GuzzleHttp\Client;
use Monolog\Logger;

class SlackMessenger
{
    private $httpClient;
    private $endpoint;
    private $notificationChannel;
    private $logChannel;
    private $logger;
    private $disableDelivery;

    public function __construct(string $endpoint, string $notificationChannel, string $logChannel, bool $disableDelivery, Logger $logger)
    {
        $this->httpClient = new Client(['timeout' => 2]);
        $this->endpoint = $endpoint;
        $this->notificationChannel = $notificationChannel;
        $this->logChannel = $logChannel;
        $this->logger = $logger;
        $this->disableDelivery = $disableDelivery;
    }

    public function notify(string $messageBody)
    {
        $this->sendPayload([
            'channel' => $this->notificationChannel,
            'text' => $messageBody,
        ]);
    }

    public function log(string $messageBody, array $attachmentData = [])
    {
        $payload = [
            'channel' => $this->logChannel,
        ];

        $attachment = $this->buildAttachment($attachmentData);
        if ($attachment !== null) {
            $payload['attachments'] = [$attachment];
        } else {
            $payload['text'] = $messageBody;
        }

        $this->sendPayload($payload);
    }

    public function messageDepartment(string $messageBody, Department $department)
    {
        if (!$department->getSlackChannel()) {
            return;
        }

        $this->sendPayload([
            'channel' => $department->getSlackChannel(),
            'text' => $messageBody,
        ]);
    }

    /**
     * Send a raw payload to Slack. Used by SlackSms and SlackMailer.
     */
    public function sendPayload(array $payload)
    {
        $channel = $payload['channel'] ?? $this->logChannel;
        $payload['channel'] = $channel;
        $payload['username'] = $payload['username'] ?? 'vektorbot';
        $payload['icon_emoji'] = $payload['icon_emoji'] ?? ':robot_face:';

        if (!$this->disableDelivery) {
            try {
                $this->httpClient->post($this->endpoint, [
                    'json' => $payload,
                ]);
            } catch (Exception $e) {
                $this->logger->error("Sending message to Slack failed! {$e->getMessage()}");
            }
        }

        $text = $payload['text'] ?? '[attachment]';
        $this->logger->info("Slack message sent to {$channel}: {$text}");
    }

    private function buildAttachment(array $data): ?array
    {
        $attachment = [];
        $hasData = false;

        foreach (['color', 'author_name', 'author_icon', 'text', 'footer'] as $key) {
            if (isset($data[$key])) {
                $attachment[$key] = $data[$key];
                $hasData = true;
            }
        }

        return $hasData ? $attachment : null;
    }
}
