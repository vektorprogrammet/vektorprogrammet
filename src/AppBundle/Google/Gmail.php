<?php

namespace AppBundle\Google;

use AppBundle\Mailer\MailerInterface;
use Google_Service_Gmail_Message;
use Swift_Message;

class Gmail extends GoogleService implements MailerInterface
{
    private $defaultEmail;

    public function send(Swift_Message $message, bool $disableLogging = false)
    {
        if ($this->disabled) {
            if (!$disableLogging) {
                $this->logger->info("Google API disabled. Did not send email to {$this->recipientsToHeader($message->getTo())}: `{$message->getSubject()}`");
            }
            return;
        }
        
        $message->setFrom([$this->defaultEmail => "Vektorprogrammet"]);

        $client = $this->getClient();
        $service = new \Google_Service_Gmail($client);
        $gmailMessage = $this->SwiftMessageToGmailMessage($message);

        try {
            $res = $service->users_messages->send($this->defaultEmail, $gmailMessage);
        } catch (\Google_Service_Exception $e) {
            $this->logServiceException($e, "Failed to send email to {$this->recipientsToHeader($message->getTo())}: `{$message->getSubject()}`");
            return;
        }

        if (array_search('SENT', $res->getLabelIds()) !== false && !$disableLogging) {
            $this->logger->info("Email sent to {$this->recipientsToHeader($message->getTo())}: `{$message->getSubject()}`");
        } else {
            $this->logger->notice(
                "Failed to send email to {$this->recipientsToHeader($message->getTo())}: `{$message->getSubject()}`\n".
                "```".
                implode(", ", $res->getLabelIds()).
                "```"
            );
        }
    }

    private function SwiftMessageToGmailMessage(Swift_Message $message)
    {
        $subject = $message->getSubject();
        $body = $this->encodeBody($message->getBody());
        $from = $this->recipientsToHeader($message->getFrom());
        $to = $this->recipientsToHeader($message->getTo());
        $replyTo = $this->recipientsToHeader($message->getReplyTo());
        $cc = $this->recipientsToHeader($message->getCc());
        $bcc = $this->recipientsToHeader($message->getBcc());
        $contentType = $message->getContentType();
        $charset = $message->getCharset();

        $strRawMessage = "From: $from\r\n";
        $strRawMessage .= "To: $to\r\n";
        if ($cc) {
            $strRawMessage .= "CC: $cc\r\n";
        }
        if ($bcc) {
            $strRawMessage .= "BCC: $bcc\r\n";
        }
        if ($replyTo) {
            $strRawMessage .= "Reply-To: $replyTo\r\n";
        }
        $strRawMessage .= 'Subject: =?utf-8?B?' . base64_encode($subject) . "?=\r\n";
        $strRawMessage .= "MIME-Version: 1.0\r\n";
        $strRawMessage .= "Content-Type: $contentType; charset=$charset\r\n";
        $strRawMessage .= 'Content-Transfer-Encoding: quoted-printable' . "\r\n\r\n";
        $strRawMessage .= "$body";

        $mime = rtrim(strtr(base64_encode($strRawMessage), '+/', '-_'), '=');
        $msg = new Google_Service_Gmail_Message();
        $msg->setRaw($mime);
        
        return $msg;
    }

    private function recipientsToHeader($recipients): string
    {
        if (!$recipients) {
            return false;
        }

        $header = "";
        foreach ($recipients as $email => $name) {
            if (strlen($header) !== 0) {
                $header .= ", ";
            }

            if ($name) {
                $header .= "$name <$email>";
            } else {
                $header .= "$email";
            }
        }

        return $header;
    }

    private function encodeBody($body)
    {
        $body = str_replace("src=\"", "src=3D\"", $body);
        $body = str_replace("src='", "src=3D'", $body);
        $body = str_replace("href=\"", "href=3D\"", $body);
        $body = str_replace("href='", "href=3D'", $body);

        return $body;
    }

    /**
     * @param string $defaultEmail
     */
    public function setDefaultEmail($defaultEmail)
    {
        $this->defaultEmail = $defaultEmail;
    }
}
