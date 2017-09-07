<?php

namespace AppBundle\Command;

use AppBundle\Service\RoleManager;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateUserRolesCommand extends ContainerAwareCommand
{
    /**
     * @var ObjectManager
     */
    private $entityManager;

    /**
     * @var RoleManager
     */
    private $roleManager;

    /**
     * @var int
     */
    private $rolesUpdatedCount;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            // a good practice is to use the 'app:' prefix to group all your custom application commands
            ->setName('app:update:roles')
            ->setDescription('Updates all user roles')
            ->setHelp(<<<'HELP'
The <info>%command.name%</info> command will update all user roles:
  <info>php %command.full_name%</info>
Assistant users that are in teams will be promoted to Team members.
Users NOT in team will be demoted to Assistants.
HELP
            );
    }
    /**
     * This method is executed before the the execute() method. It's main purpose
     * is to initialize the variables used in the rest of the command methods.
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->entityManager = $this->getContainer()->get('doctrine')->getManager();
        $this->roleManager = $this->getContainer()->get('app.roles');

        $this->rolesUpdatedCount = 0;
    }
    /**
     * This method is executed after initialize(). It usually contains the logic
     * to execute to complete this command task.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $startTime = microtime(true);

        $users = $this->entityManager->getRepository('AppBundle:User')->findAll();

        foreach ($users as $user) {
            $this->roleManager->updateUserRole($user);
        }

        $this->entityManager->flush();

        $finishTime = microtime(true);
        $elapsedTime = ($finishTime - $startTime) * 1000;

        $output->writeln(sprintf('%d roles updated in %d ms', $this->rolesUpdatedCount, $elapsedTime));
    }
}
