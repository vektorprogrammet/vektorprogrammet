<?php

namespace AppBundle\Command;

use AppBundle\Service\AdmissionNotifier;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendInfoMeetingNotificationsCommand extends ContainerAwareCommand
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
            ->setName('app:admission:send_info_meeting_notifications')
            ->setDescription('Sends notifications about info meeting to subscribers');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->notifier = $this->getContainer()->get(AdmissionNotifier::class);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->notifier->sendInfoMeetingNotifications();
    }
}
