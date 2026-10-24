<?php

namespace AppBundle\Service;

use AppBundle\Entity\Department;
use Exception;
use Monolog\Logger;
use AppBundle\Slack\Attachment;
use AppBundle\Slack\Message;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SlackMessenger
{
    private $slackClient;
    private $notificationChannel;
    private $logChannel;
    private $logger;
    private $disableDelivery;
    private $endpoint;

    /**
     * SlackMessenger constructor.
     *
     * @param Client $slackClient
     * @param string $notificationChannel
     * @param string $logChannel
     * @param bool   $disableDelivery
     * @param Logger $logger
     */
    public function __construct(HttpClientInterface $slackClient, string $endpoint, string $notificationChannel, string $logChannel, bool $disableDelivery, Logger $logger)
    {
        $this->slackClient = $slackClient;
        $this->endpoint = $endpoint;
        $this->notificationChannel = $notificationChannel;
        $this->logChannel = $logChannel;
        $this->logger = $logger;
        $this->disableDelivery = $disableDelivery;
    }

    public function notify(string $messageBody)
    {
        $message = $this->createMessage();

        $message
            ->to($this->notificationChannel)
            ->setText($messageBody);

        $this->send($message);
    }

    public function log(string $messageBody, array $attachmentData = [])
    {
        $message = $this->createMessage();
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

        $message = $this->createMessage();

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
                $response = $this->slackClient->request('POST', $this->endpoint, [
                    'json' => $message->jsonSerialize(),
                    'timeout' => 2,
                ]);
                if ($response->getStatusCode() !== 200) {
                    $this->logger->error('Slack returned an unsuccessful response.');
                    return;
                }
            } catch (Exception $e) {
                // HTTP exception messages may contain the secret webhook URL.
                $this->logger->error('Sending message to Slack failed.', ['exception_class' => get_class($e)]);
                return;
            }
        }

        $this->logger->info($this->disableDelivery ? 'Slack delivery disabled.' : 'Slack message sent.', ['channel' => $message->getChannel()]);
    }
    
    public function createMessage(): Message
    {
        return new Message();
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
