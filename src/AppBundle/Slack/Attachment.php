<?php

namespace AppBundle\Slack;

final class Attachment implements \JsonSerializable
{
    private array $data = [];

    public function setColor(?string $value): self { $this->data['color'] = $value; return $this; }
    public function setAuthorName(?string $value): self { $this->data['author_name'] = $value; return $this; }
    public function setAuthorIcon(?string $value): self { $this->data['author_icon'] = $value; return $this; }
    public function setText(?string $value): self { $this->data['text'] = $value; return $this; }
    public function setFooter(?string $value): self { $this->data['footer'] = $value; return $this; }
    public function jsonSerialize(): array { return $this->data; }
}
