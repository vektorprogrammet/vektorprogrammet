<?php

namespace AppBundle\Command;

use AppBundle\Service\MigrationMetadataUpgrader;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:migrations:upgrade-metadata', description: 'Preview or convert legacy migration identifiers; does not execute migrations.')]
final class UpgradeMigrationMetadataCommand extends Command
{
    public function __construct(private MigrationMetadataUpgrader $upgrader)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('apply', null, InputOption::VALUE_NONE, 'Apply the conversion after backing up and reviewing the preview.');
        $this->addOption('check', null, InputOption::VALUE_NONE, 'Fail if conversion is required, for deployment preflight.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $changes = $this->upgrader->plan();
        if ($changes === []) {
            $io->success('No legacy migration identifiers require conversion.');
            return self::SUCCESS;
        }

        $rows = [];
        foreach ($changes as $old => $new) {
            $rows[] = [(string) $old, $new];
        }
        $io->table(['Recorded version', 'Updated identifier'], $rows);
        if ($input->getOption('check')) {
            $io->error('Review and convert the legacy migration history before running migrations.');
            return self::FAILURE;
        }
        if (!$input->getOption('apply')) {
            $io->note('Preview only. Back up the database and review this mapping before using --apply.');
            return self::SUCCESS;
        }
        $count = $this->upgrader->apply();
        $io->success(sprintf('Converted %d identifiers. No application migrations were executed.', $count));
        return self::SUCCESS;
    }
}
