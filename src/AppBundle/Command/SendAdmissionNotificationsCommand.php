<?php

namespace AppBundle\Command;

use AppBundle\Service\AdmissionNotifier;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendAdmissionNotificationsCommand extends ContainerAwareCommand
{
    /**
     * @var AdmissionNotifier
     */
    private $notifier;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:admission:send_notifications')
            ->setDescription('Sends notifications about active admission period to subscribers');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->notifier = $this->getContainer()->get(AdmissionNotifier::class);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->notifier->sendAdmissionNotifications();
    }
}
