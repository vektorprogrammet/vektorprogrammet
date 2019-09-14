<?php
namespace AppBundle\Service;

use AppBundle\Entity\Feedback;

class FeedbackMessenger
{
    private $messenger;
    public function __construct(SlackMessenger $messenger)
    {
        $this->messenger = $messenger;
    }
    public function sendMessage(Feedback $feedback)
    {
        $message = $this->messenger->createMessage();
        $message->to = "#it-general";
        $message->text = $feedback->description;
    }
}