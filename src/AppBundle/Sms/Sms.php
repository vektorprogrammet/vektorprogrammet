<?php

namespace AppBundle\Sms;

class Sms
{
    private $sender;
    private $message;
    private $recipients;

    public function setSender(string $sender)
    {
        $this->sender = $sender;
    }

    public function getSender()
    {
        return $this->sender;
    }

    public function setMessage(string $message)
    {
        $this->message = $message;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setRecipients(array $recipients)
    {
        $this->recipients = $recipients;
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
