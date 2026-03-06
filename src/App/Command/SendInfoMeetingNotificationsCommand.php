<?php

namespace App\Command;

use App\Service\AdmissionNotifier;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendInfoMeetingNotificationsCommand extends Command
{
    public function __construct(private readonly AdmissionNotifier $notifier)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:admission:send_info_meeting_notifications')
            ->setDescription('Sends notifications about info meeting to subscribers');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->notifier->sendInfoMeetingNotifications();

        return Command::SUCCESS;
    }
}
