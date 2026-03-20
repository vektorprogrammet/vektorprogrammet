<?php

namespace App\Identity\Infrastructure\Command;

use App\Identity\Infrastructure\Entity\User;
use App\Identity\Infrastructure\RoleManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateUserRolesCommand extends Command
{
    private int $rolesUpdatedCount = 0;

    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly RoleManager $roleManager)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            // a good practice is to use the 'app:' prefix to group all your custom application commands
            ->setName('app:update:roles')
            ->setDescription('Updates all user roles')
            ->setHelp(
                <<<'HELP'
                    The <info>%command.name%</info> command will update all user roles:
                      <info>php %command.full_name%</info>
                    Assistant users that are in teams will be promoted to Team members.
                    Users NOT in team will be demoted to Assistants.
                    HELP
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $startTime = microtime(true);

        $users = $this->entityManager->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            $roleUpdated = $this->roleManager->updateUserRole($user);
            if ($roleUpdated) {
                ++$this->rolesUpdatedCount;
            }
        }

        $this->entityManager->flush();

        $finishTime = microtime(true);
        $elapsedTime = ($finishTime - $startTime) * 1000;

        $output->writeln(sprintf('%d roles updated in %d ms', $this->rolesUpdatedCount, $elapsedTime));

        return Command::SUCCESS;
    }
}
