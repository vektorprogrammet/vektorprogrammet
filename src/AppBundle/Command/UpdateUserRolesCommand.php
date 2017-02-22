<?php

namespace AppBundle\Command;

use AppBundle\Entity\User;
use AppBundle\Role\Roles;
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
            $this->updateUserRole($user);
        }

        $finishTime = microtime(true);
        $elapsedTime = ($finishTime - $startTime) * 1000;

        $output->writeln(sprintf('%d roles updated in %d ms', $this->rolesUpdatedCount, $elapsedTime));
    }

    private function updateUserRole(User $user)
    {
        if ($this->userIsInATeam($user)) {
            $this->promoteUserToTeamMember($user);
        } else {
            $this->demoteUserToAssistant($user);
        }

        $this->entityManager->flush();
    }

    private function userIsInATeam(User $user)
    {
        $department = $user->getDepartment();
        $semester = $department->getCurrentOrLatestSemester();
        $workHistories = $user->getWorkHistories();

        if ($semester === null) {
            return false;
        }

        foreach ($workHistories as $workHistory) {
            if ($workHistory->isActiveInSemester($semester)) {
                return true;
            }
        }

        return false;
    }

    private function promoteUserToTeamMember(User $user)
    {
        if ($this->userIsAssistant($user)) {
            $this->setUserRole($user, Roles::TEAM_MEMBER);
        }
    }

    private function demoteUserToAssistant(User $user)
    {
        if (!$this->userIsAssistant($user)) {
            $this->setUserRole($user, Roles::ASSISTANT);
        }
    }

    private function userIsAssistant(User $user)
    {
        return  current($user->getRoles())->getRole() === Roles::ASSISTANT;
    }

    private function setUserRole(User $user, string $role)
    {
        $isValidRole = $this->getContainer()->get('app.roles')->isValidRole($role);
        if (!$isValidRole) {
            throw new \InvalidArgumentException("Invalid role $role");
        }

        $role = $this->entityManager->getRepository('AppBundle:Role')->findByRoleName($role);
        $user->setRoles([$role]);
        $this->entityManager->persist($user);

        ++$this->rolesUpdatedCount;
    }
}
