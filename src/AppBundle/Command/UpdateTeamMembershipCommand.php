<?php

namespace AppBundle\Command;

use AppBundle\Service\TeamMembershipService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateTeamMembershipCommand extends ContainerAwareCommand
{
    /**
     * @var TeamMembershipService
     */
    private $notifier;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:update:team_membership')
            ->setDescription('Looks for expired team memberships');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->notifier = $this->getContainer()->get(TeamMembershipService::class);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->notifier->updateTeamMemberships();
    }
}
