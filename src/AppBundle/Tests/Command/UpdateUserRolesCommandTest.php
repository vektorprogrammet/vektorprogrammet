<?php

namespace AppBundle\Tests\Command;

use AppBundle\Command\UpdateUserRolesCommand;
use AppBundle\Role\Roles;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UpdateUserRolesCommandTest extends KernelTestCase
{
    private $em;
    private $users;

    protected function setUp()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $this->em = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        $this->users = [
            new MockUser('assistant@gmail.com', Roles::ASSISTANT, Roles::ASSISTANT),
            new MockUser('kristoffer@stud.ntnu.no', Roles::ASSISTANT, Roles::TEAM_MEMBER),
            new MockUser('aai@b.c', Roles::TEAM_MEMBER, Roles::ASSISTANT),
            new MockUser('team@gmail.com', Roles::TEAM_MEMBER, Roles::ASSISTANT),
            new MockUser('sortland@mail.com', Roles::TEAM_MEMBER, Roles::TEAM_MEMBER),
            new MockUser('admin@gmail.com', Roles::TEAM_LEADER, Roles::ASSISTANT),
            new MockUser('aah@b.c', Roles::TEAM_LEADER, Roles::TEAM_LEADER),
            new MockUser('jan-per-gustavio@gmail.com', Roles::TEAM_LEADER, Roles::TEAM_LEADER), // Executive board member
            new MockUser('aaf@b.c', Roles::TEAM_MEMBER, Roles::TEAM_LEADER), // Executive board member
            new MockUser('superadmin@gmail.com', Roles::ADMIN, Roles::ASSISTANT),
            new MockUser('petter@stud.ntnu.no', Roles::ADMIN, Roles::ADMIN),
        ];
    }

    public function testUserRolesBeforeExecution()
    {
        foreach ($this->users as $user) {
            $this->assertThatUserWithEmailHasRole($user->getEmail(), $user->getRoleBeforeExecution());
        }
    }

    public function testExecute()
    {
        $this->executeCommand();

        foreach ($this->users as $user) {
            $this->assertThatUserWithEmailHasRole($user->getEmail(), $user->getRoleAfterExecution());
        }

        \TestDataManager::restoreDatabase();
    }

    private function executeCommand()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $application = new Application($kernel);
        $application->add(new UpdateUserRolesCommand());

        $command = $application->find('app:update:roles');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
        ));
    }

    private function assertThatUserWithEmailHasRole(string $email, string $role)
    {
        $user = $this->em->getRepository('AppBundle:User')->findUserByEmail($email);
        $this->assertEquals($role, current($user->getRoles())->getRole());
    }
}

class MockUser
{
    private $email;
    private $roleBeforeExecution;
    private $roleAfterExecution;

    /**
     * MockUser constructor.
     *
     * @param $email
     * @param $roleBeforeExecution
     * @param $roleAfterExecution
     */
    public function __construct($email, $roleBeforeExecution, $roleAfterExecution)
    {
        $this->email = $email;
        $this->roleBeforeExecution = $roleBeforeExecution;
        $this->roleAfterExecution = $roleAfterExecution;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getRoleBeforeExecution()
    {
        return $this->roleBeforeExecution;
    }

    /**
     * @return string
     */
    public function getRoleAfterExecution()
    {
        return $this->roleAfterExecution;
    }
}
