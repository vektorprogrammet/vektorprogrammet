<?php

namespace AppBundle\Sms;

use AppBundle\Service\LogService;

class Sender
{
    private $landCode;

    private $token;
    private $logger;

    public function __construct(string $token, LogService $logger)
    {
        $this->landCode = '47';
        $this->token = $token;
        $this->logger = $logger;
    }
    
    public function send(Sms $sms)
    {
        $data = array(
            'token' => $this->token,
            'sender' => $sms->getSender(),
            'message' => $sms->getMessage()
        );

        $recipients = $sms->getRecipients();
        $c = 0;
        foreach ($recipients as $recipient) {
            $number = $this->cleanPhoneNumber($recipient);
            if ($number !== false) {
                $data["recipients.$c.msisdn"] = $number;
                $c++;
            } else {
                $this->logger->alert("Could not send sms to $recipient");
            }
        }

        $query = http_build_query($data);
        $result = file_get_contents('https://gatewayapi.com/rest/mtsms?' . $query);
        $this->log($sms);
        $this->logger->info(print_r(json_decode($result)->ids, true));
    }

    private function log(Sms $sms)
    {
        $recipients = $sms->getRecipients();
        $recipientsString = "";
        for ($i = 0; $i < count($recipients); $i++) {
            $recipientsString .= $recipients[$i];
            if ($i !== count($recipients)-1) {
                $recipientsString .= ", ";
            }
        }

        $logMessage =
            "SMS sent\n" .
            "```\n" .
            "To: $recipientsString\n" .
            "Sender: {$sms->getSender()}" .
            "Message: {$sms->getMessage()}" .
            "```\n";
        $this->logger->info($logMessage);
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
