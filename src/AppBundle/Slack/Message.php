<?php

namespace AppBundle\Slack;

final class Message implements \JsonSerializable
{
    private ?string $channel = null;
    private string $text = '';
    private array $attachments = [];

    public function to(string $channel): self { return $this->setChannel($channel); }
    public function setChannel(string $channel): self { $this->channel = $channel; return $this; }
    public function getChannel(): ?string { return $this->channel; }
    public function setText(string $text): self { $this->text = $text; return $this; }
    public function getText(): string { return $this->text; }
    public function setAttachments(array $attachments): self { $this->attachments = $attachments; return $this; }

    public function jsonSerialize(): array
    {
        return array_filter([
            'username' => 'vektorbot',
            'icon_emoji' => ':robot_face:',
            'channel' => $this->channel,
            'text' => $this->text,
            'attachments' => $this->attachments,
        ], static fn ($value) => $value !== null && $value !== []);
    }
}
