<?php

namespace AppBundle\Command;

use AppBundle\Service\InterviewManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendAcceptInterviewReminderCommand extends ContainerAwareCommand
{
    /**
     * @var InterviewManager
     */
    private $interviewManager;

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('app:send_accept_interview_reminder')
            ->setDescription('Send an email reminder to all users with unaccepted interviews');
    }

    /**
     * @inheritdoc
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->interviewManager = $this->getContainer()->get(InterviewManager::class);
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->interviewManager->sendAcceptInterviewReminders();
    }
}
