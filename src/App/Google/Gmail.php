<?php

namespace App\Google;

use App\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class Gmail extends GoogleService implements MailerInterface
{
    private $defaultEmail;

    public function send(Email $message, bool $disableLogging = false)
    {
        if ($this->disabled) {
            if (!$disableLogging) {
                $this->logger->info("Google API disabled. Did not send email to {$this->addressesToHeader($message->getTo())}: `{$message->getSubject()}`");
            }

            return;
        }

        $message->from(new Address($this->defaultEmail, 'Vektorprogrammet'));

        $client = $this->getClient();
        $service = new \Google_Service_Gmail($client);
        $gmailMessage = $this->emailToGmailMessage($message);

        try {
            $res = $service->users_messages->send($this->defaultEmail, $gmailMessage);
        } catch (\Google_Service_Exception $e) {
            $this->logServiceException($e, "Failed to send email to {$this->addressesToHeader($message->getTo())}: `{$message->getSubject()}`");

            return;
        }

        if (array_search('SENT', $res->getLabelIds()) !== false && !$disableLogging) {
            $this->logger->info("Email sent to {$this->addressesToHeader($message->getTo())}: `{$message->getSubject()}`");
        } else {
            $this->logger->notice(
                "Failed to send email to {$this->addressesToHeader($message->getTo())}: `{$message->getSubject()}`\n".
                '```'.
                implode(', ', $res->getLabelIds()).
                '```'
            );
        }
    }

    private function emailToGmailMessage(Email $message)
    {
        $subject = $message->getSubject();
        $body = $this->encodeBody($message->getHtmlBody() ?: $message->getTextBody());
        $from = $this->addressesToHeader($message->getFrom());
        $to = $this->addressesToHeader($message->getTo());
        $replyTo = $this->addressesToHeader($message->getReplyTo());
        $cc = $this->addressesToHeader($message->getCc());
        $bcc = $this->addressesToHeader($message->getBcc());
        $contentType = $message->getHtmlBody() ? 'text/html' : 'text/plain';
        $charset = 'utf-8';

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
        $strRawMessage .= 'Subject: =?utf-8?B?'.base64_encode((string) $subject)."?=\r\n";
        $strRawMessage .= "MIME-Version: 1.0\r\n";
        $strRawMessage .= "Content-Type: $contentType; charset=$charset\r\n";
        $strRawMessage .= 'Content-Transfer-Encoding: quoted-printable'."\r\n\r\n";
        $strRawMessage .= "$body";

        $mime = rtrim(strtr(base64_encode($strRawMessage), '+/', '-_'), '=');
        $msg = new \Google_Service_Gmail_Message();
        $msg->setRaw($mime);

        return $msg;
    }

    private function addressesToHeader(array $addresses): string
    {
        if (empty($addresses)) {
            return '';
        }

        $parts = [];
        foreach ($addresses as $address) {
            if ($address instanceof Address) {
                if ($address->getName()) {
                    $parts[] = "{$address->getName()} <{$address->getAddress()}>";
                } else {
                    $parts[] = $address->getAddress();
                }
            } else {
                $parts[] = (string) $address;
            }
        }

        return implode(', ', $parts);
    }

    private function encodeBody($body)
    {
        $body = str_replace('src="', 'src=3D"', $body);
        $body = str_replace("src='", "src=3D'", $body);
        $body = str_replace('href="', 'href=3D"', $body);
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
