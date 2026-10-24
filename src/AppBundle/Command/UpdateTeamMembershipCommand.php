<?php

namespace AppBundle\Command;

use AppBundle\Service\TeamMembershipService;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateTeamMembershipCommand extends ContainerCommand
{
    /**
     * @var TeamMembershipService
     */
    private $notifier;

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('app:update:team_membership')
            ->setDescription('Looks for expired team memberships');
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->notifier = $this->getContainer()->get(TeamMembershipService::class);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->notifier->updateTeamMemberships();
        return self::SUCCESS;
    }
}
