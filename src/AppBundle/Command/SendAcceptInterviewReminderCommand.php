<?php

namespace AppBundle\Command;

use AppBundle\Service\InterviewManager;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendAcceptInterviewReminderCommand extends ContainerCommand
{
    /**
     * @var InterviewManager
     */
    private $interviewManager;

    /**
     * @inheritdoc
     */
    protected function configure(): void
    {
        $this
            ->setName('app:send_accept_interview_reminder')
            ->setDescription('Send an email reminder to all users with unaccepted interviews');
    }

    /**
     * @inheritdoc
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->interviewManager = $this->getContainer()->get(InterviewManager::class);
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->interviewManager->sendAcceptInterviewReminders();
        return self::SUCCESS;
    }
}
