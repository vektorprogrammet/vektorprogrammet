<?php

namespace AppBundle\Sms;

use AppBundle\Service\LogService;

class GatewayAPI implements SmsSenderInterface
{
    private $apiToken;
    private $logger;
    private $disableDelivery;
    private $maxLength;
    private $countryCode;

    public function __construct(array $smsOptions, LogService $logger)
    {
        $this->logger = $logger;
        $this->disableDelivery = $smsOptions['disable_delivery'];
        $this->maxLength = $smsOptions['max_length'];
        $this->apiToken = $smsOptions['api_token'];
        $this->countryCode = $smsOptions['country_code'];
    }
    
    public function send(Sms $sms)
    {
        if (strlen($sms->getMessage()) > $this->maxLength) {
            $this->logMessageTooLong($sms);
            return;
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
            "Sender: {$sms->getSender()}\n" .
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

        $startsWithCountryCode =
            strlen($number) === 8 + strlen($countryCode) &&
            $this->startsWith($number, $countryCode);
        $startsWithPlusCountryCode =
            strlen($number) === 9 + strlen($countryCode) &&
            $this->startsWith($number, "+$countryCode");

        if (strlen($number) === 8) {
            return $countryCode . $number;
        } elseif ($startsWithCountryCode) {
            return $number;
        } elseif ($startsWithPlusCountryCode) {
            return substr($number, 1);
        } else {
            return false;
        }
    }

    public function validatePhoneNumber(string $number): bool
    {
        $countryCode = $this->countryCode;
        $number = preg_replace('/\s+/', '', $number);

        $startsWithCountryCode =
            strlen($number) === 8 + strlen($countryCode) &&
            $this->startsWith($number, $countryCode);
        $startsWithPlusCountryCode =
            strlen($number) === 9 + strlen($countryCode) &&
            $this->startsWith($number, "+$countryCode");

        return
            strlen($number) === 8 ||
            $startsWithCountryCode ||
            $startsWithPlusCountryCode;
    }

    private function startsWith(string $string, string $search)
    {
        return substr($string, 0, strlen($search)) === $search;
    }
}
