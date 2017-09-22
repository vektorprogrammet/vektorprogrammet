<?php

namespace AppBundle\Sms;

use AppBundle\Service\LogService;

class Sender
{
    private $landCode;

    private $token;
    private $logger;
    private $disableDelivery;
    private $maxLength;

    public function __construct(string $token, LogService $logger, bool $disableDelivery, string $maxLength)
    {
        $this->landCode = '47';
        $this->token = $token;
        $this->logger = $logger;
        $this->disableDelivery = $disableDelivery;
        $this->maxLength = $maxLength;
    }
    
    public function send(Sms $sms)
    {
        if (strlen($sms->getMessage()) > $this->maxLength) {
            $this->logMessageTooLong($sms);
        }

        $data = array(
            'token' => $this->token,
            'sender' => $sms->getSender(),
            'message' => $sms->getMessage()
        );

        $recipients = $sms->getRecipients();
        $i = 0;
        foreach ($recipients as $recipient) {
            $number = $this->cleanPhoneNumber($recipient);
            if ($number !== false) {
                $data["recipients.$i.msisdn"] = $number;
                $i++;
            } else {
                $this->logger->alert("Could not send sms to *$recipient*, invalid phone number");
            }
        }

        if (!$this->disableDelivery) {
            $query = http_build_query($data);
            file_get_contents('https://gatewayapi.com/rest/mtsms?' . $query);
        }

        $this->log($sms);
    }

    private function log(Sms $sms)
    {
        $recipientsString = $sms->getRecipientsString();

        $logMessage =
            "SMS sent\n" .
            "```\n" .
            "To: $recipientsString\n" .
            "Sender: {$sms->getSender()}" .
            "Message: {$sms->getMessage()}" .
            "```\n";
        $this->logger->info($logMessage);
    }

    private function logMessageTooLong(Sms $sms)
    {
        $smsLength = strlen($sms->getMessage());
        $message =
            "Could not send SMS to *{$sms->getRecipientsString()}*: " .
            "Message too long ($smsLength/$this->maxLength characters)\n\n" .
            "```\n" .
            $sms->getMessage() .
            "```\n";

        $this->logger->alert($message);
    }

    public function cleanPhoneNumber(string $number)
    {
        $number = preg_replace('/\s+/', '', $number);

        if (strlen($number) === 8) {
            return $this->landCode . $number;
        } elseif (strlen($number) === 11 && substr($number, 0, 3) === "+{$this->landCode}") {
            return $this->landCode . substr($number, 3);
        } elseif (strlen($number) === 10 && substr($number, 0, 2) === $this->landCode) {
            return $number;
        } else {
            return false;
        }
    }
}
