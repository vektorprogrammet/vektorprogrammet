<?php

namespace App\Admission\Infrastructure\Command;

use App\Admission\Infrastructure\AdmissionNotifier;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendAdmissionNotificationsCommand extends Command
{
    public function __construct(private readonly AdmissionNotifier $notifier)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:admission:send_notifications')
            ->setDescription('Sends notifications about active admission period to subscribers');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->notifier->sendAdmissionNotifications();

        return Command::SUCCESS;
    }
}
