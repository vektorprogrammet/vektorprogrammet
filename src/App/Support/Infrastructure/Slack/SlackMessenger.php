<?php

namespace App\Support\Infrastructure\Slack;

use App\Organization\Infrastructure\Entity\Department;
use GuzzleHttp\Client;
use Monolog\Logger;

class SlackMessenger
{
    private $httpClient;

    public function __construct(
        private readonly string $endpoint,
        private readonly string $notificationChannel,
        private readonly string $logChannel,
        private readonly bool $disableDelivery,
        private readonly Logger $logger,
    ) {
        $this->httpClient = new Client(['timeout' => 2]);
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
        $payload['username'] ??= 'vektorbot';
        $payload['icon_emoji'] ??= ':robot_face:';

        if (!$this->disableDelivery) {
            try {
                $this->httpClient->post($this->endpoint, [
                    'json' => $payload,
                ]);
            } catch (\Exception $e) {
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
