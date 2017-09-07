<?php

namespace AppBundle\Tests\Command;

use AppBundle\Command\UpdateUserRolesCommand;
use AppBundle\Role\Roles;
use AppBundle\Service\RoleManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RoleManagerTest extends KernelTestCase
{
    /**
     * @var RoleManager
     */
    private $roleManager;
    /**
     * @var EntityManager
     */
    private $em;
    /**
     * @var MockRoleUser[]
     */
    private $users;

    protected function setUp()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $this->em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->roleManager = $kernel->getContainer()->get('app.roles');

        $this->users = [
            new MockRoleUser('assistant@gmail.com', Roles::ASSISTANT, Roles::ASSISTANT),
            new MockRoleUser('kristoffer@stud.ntnu.no', Roles::ASSISTANT, Roles::TEAM_MEMBER),
            new MockRoleUser('aai@b.c', Roles::TEAM_MEMBER, Roles::ASSISTANT),
            new MockRoleUser('team@gmail.com', Roles::TEAM_MEMBER, Roles::ASSISTANT),
            new MockRoleUser('sortland@mail.com', Roles::TEAM_MEMBER, Roles::TEAM_MEMBER),
            new MockRoleUser('admin@gmail.com', Roles::TEAM_LEADER, Roles::ASSISTANT),
            new MockRoleUser('aah@b.c', Roles::TEAM_LEADER, Roles::TEAM_LEADER),
            new MockRoleUser('jan-per-gustavio@gmail.com', Roles::TEAM_LEADER, Roles::TEAM_LEADER), // Executive board member
            new MockRoleUser('superadmin@gmail.com', Roles::ADMIN, Roles::ASSISTANT),
            new MockRoleUser('petter@stud.ntnu.no', Roles::ADMIN, Roles::ADMIN),
        ];
    }

    public function testUserRolesBeforeExecution()
    {
        foreach ($this->users as $user) {
            $this->assertThatUserWithEmailHasRole($user->getEmail(), $user->getRoleBeforeExecution());
        }
    }

    public function testUpdateRole()
    {
        $this->updateAllUserRoles();

        foreach ($this->users as $user) {
            $this->assertThatUserWithEmailHasRole($user->getEmail(), $user->getRoleAfterExecution());
        }

        \TestDataManager::restoreDatabase();
    }

    private function updateAllUserRoles()
    {
        $realUsers = $this->em->getRepository('AppBundle:User')->findAll();
        foreach ($realUsers as $u) {
            $this->roleManager->updateUserRole($u);
        }
    }

    private function assertThatUserWithEmailHasRole(string $email, string $role)
    {
        $user = $this->em->getRepository('AppBundle:User')->findUserByEmail($email);
        $this->assertEquals($role, current($user->getRoles())->getRole());
    }
}

class MockRoleUser
{
    private $email;
    private $roleBeforeExecution;
    private $roleAfterExecution;

    /**
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
