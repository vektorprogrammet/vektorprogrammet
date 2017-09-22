<?php

namespace AppBundle\Sms;

class Sms
{
    private $sender;
    private $message;
    private $recipients;
    
    public function sender(string $sender): self
    {
        $this->sender = $sender;
        return $this;
    }

    public function message(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function recipients(array $recipients): self
    {
        $this->recipients = $recipients;
        return $this;
    }

    public function getSender()
    {
        return $this->sender;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getRecipients()
    {
        return $this->recipients;
    }

    public function getRecipientsString()
    {
        $recipientsString = "";
        for ($i = 0; $i < count($this->recipients); $i++) {
            $recipientsString .= $this->recipients[$i];
            if ($i !== count($this->recipients)-1) {
                $recipientsString .= ", ";
            }
        }

        return $recipientsString;
    }
}
