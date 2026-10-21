<?php

namespace AppBundle\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/** Captures local/test mail without making an SMTP connection. */
final class MailCollector extends DataCollector implements \Swift_Events_SendListener
{
    private array $messages = [];

    public function beforeSendPerformed(\Swift_Events_SendEvent $event): void
    {
        $this->messages[] = clone $event->getMessage();
    }

    public function sendPerformed(\Swift_Events_SendEvent $event): void
    {
    }

    public function collect(Request $request, Response $response, ?\Throwable $exception = null): void
    {
        $this->data['messages'] = $this->messages;
    }

    public function getMessages(): array
    {
        return $this->data['messages'] ?? $this->messages;
    }

    public function getMessageCount(): int
    {
        return count($this->getMessages());
    }

    public function reset(): void
    {
        $this->data = [];
        $this->messages = [];
    }

    public function getName(): string
    {
        return 'swiftmailer';
    }
}
