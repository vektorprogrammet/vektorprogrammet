<?php

namespace AppBundle\Command;

use AppBundle\Entity\Subscriber;
use AppBundle\Service\AdmissionNotifier;
use AppBundle\Service\EmailSender;
use Doctrine\Common\Persistence\ObjectManager;
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
    /**
     * This method is executed before the the execute() method. It's main purpose
     * is to initialize the variables used in the rest of the command methods.
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->notifier = $this->getContainer()->get('app.admission_notifier');
    }
    /**
     * This method is executed after initialize(). It usually contains the logic
     * to execute to complete this command task.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->notifier->sendAdmissionNotifications();
    }
}
