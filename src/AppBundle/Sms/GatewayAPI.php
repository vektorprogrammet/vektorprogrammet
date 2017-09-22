<?php

namespace AppBundle\Sms;

use AppBundle\Service\LogService;

class GatewayAPI implements SmsSender
{
    private $apiToken;
    private $logger;
    private $disableDelivery;
    private $maxLength;
    private $countryCode;

    public function __construct(string $apiToken, LogService $logger, bool $disableDelivery, string $maxLength, string $countryCode)
    {
        $this->logger = $logger;
        $this->disableDelivery = $disableDelivery;
        $this->maxLength = $maxLength;
        $this->apiToken = $apiToken;
        $this->countryCode = $countryCode;
    }
    
    public function send(Sms $sms)
    {
        if (strlen($sms->getMessage()) > $this->maxLength) {
            $this->logMessageTooLong($sms);
        }

        $data = array(
            'token' => $this->apiToken,
            'sender' => $sms->getSender(),
            'message' => $sms->getMessage()
        );

        $recipients = $sms->getRecipients();
        $i = 0;
        foreach ($recipients as $recipient) {
            $number = $this->formatPhoneNumber($recipient);
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

    public function formatPhoneNumber(string $number)
    {
        $countryCode = $this->countryCode;
        $number = preg_replace('/\s+/', '', $number);

        if (!$this->validatePhoneNumber($number)) {
            return false;
        }

        if (strlen($number) === 8) {
            return $countryCode . $number;
        } elseif (strlen($number) === 10 && $this->startsWith($number, "{$countryCode}")) {
            return $number;
        } elseif (strlen($number) === 11 && $this->startsWith($number, "+{$countryCode}")) {
            return $countryCode . substr($number, 3);
        } else {
            return false;
        }
    }

    public function validatePhoneNumber(string $number): bool
    {
        $number = preg_replace('/\s+/', '', $number);

        return
            strlen($number) === 8 ||
            strlen($number) === 10 && $this->startsWith($number, $this->countryCode) ||
            strlen($number) === 11 && $this->startsWith($number, "+$this->countryCode");
    }

    private function startsWith(string $string, string $search)
    {
        return substr($string, 0, strlen($search)) === $search;
    }
}
