<?php

namespace AppBundle\Command;

use AppBundle\Service\AdmissionNotifier;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendAdmissionNotificationsCommand extends ContainerCommand
{
    /**
     * @var AdmissionNotifier
     */
    private $notifier;

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('app:admission:send_notifications')
            ->setDescription('Sends notifications about active admission period to subscribers');
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->notifier = $this->getContainer()->get(AdmissionNotifier::class);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->notifier->sendAdmissionNotifications();
        return self::SUCCESS;
    }
}
