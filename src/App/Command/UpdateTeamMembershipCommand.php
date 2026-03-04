<?php

namespace App\Command;

use App\Service\TeamMembershipService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateTeamMembershipCommand extends Command
{
    private TeamMembershipService $teamMembershipService;

    public function __construct(TeamMembershipService $teamMembershipService)
    {
        $this->teamMembershipService = $teamMembershipService;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:update:team_membership')
            ->setDescription('Looks for expired team memberships');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->teamMembershipService->updateTeamMemberships();

        return Command::SUCCESS;
    }
}
