<?php


namespace Tests\AppBundle\Service;


use AppBundle\Entity\AccessRule;
use AppBundle\Role\Roles;
use AppBundle\Service\AccessControlService;
use Tests\BaseKernelTestCase;

class AccessControlTest extends BaseKernelTestCase {
	/**
	 * @var AccessControlService $service
	 */
	private $service;
	private $teamMember;
	private $assistant;
	private $team;
	private $teamMemberRole;
	private $executiveBoardMember;

	protected function setUp()
	{
		$kernel = $this->createKernel();
		$kernel->boot();

		$this->service = $kernel->getContainer()->get('app.access_control');
		$em = $kernel->getContainer()->get('doctrine')->getManager();
		$this->teamMember = $em->getRepository('AppBundle:User')->findUserByEmail('sortland@mail.com');
		$this->assistant = $em->getRepository('AppBundle:User')->findUserByEmail('assistent@gmail.com');
		$this->executiveBoardMember = $em->getRepository('AppBundle:User')->findUserByEmail('angela@mail.no');
		$this->team = $em->getRepository('AppBundle:Team')->find(1);
		$this->teamMemberRole = $em->getRepository('AppBundle:Role')->findByRoleName(Roles::TEAM_MEMBER);
	}

	public function testHasAccessWhenRuleDoesNotExist() {
		$access = $this->service->checkAccess( 'nonexisting' );
		$this->assertTrue( $access );
	}

	public function testCreateRule() {
		$resource = 'testRule';
		$access = $this->service->checkAccess( $resource );
		$this->assertTrue( $access );

		$rule = new AccessRule();
		$rule->setName("testName");
		$rule->setResource($resource);
		$this->service->createRule($rule);

		$access = $this->service->checkAccess( $resource );
		$this->assertFalse( $access );
	}

	public function testCreateRuleForUser() {
		$resource = 'testRule';
		$this->assertTrue($this->service->checkAccess( $resource, $this->teamMember ));
		$this->assertTrue($this->service->checkAccess( $resource, $this->assistant ));

		$rule = new AccessRule();
		$rule->setName("testName");
		$rule->setResource($resource);
		$rule->setUsers([$this->teamMember]);
		$this->service->createRule($rule);

		$this->assertTrue($this->service->checkAccess( $resource, $this->teamMember ));
		$this->assertFalse($this->service->checkAccess( $resource, $this->assistant ));
	}

	public function testCreateRuleForTeam() {
		$resource = 'testRule';
		$this->assertTrue($this->service->checkAccess( $resource, $this->teamMember ));
		$this->assertTrue($this->service->checkAccess( $resource, $this->assistant ));

		$rule = new AccessRule();
		$rule->setName("testName");
		$rule->setResource($resource);
		$rule->setTeams([$this->team]);
		$this->service->createRule($rule);

		$this->assertTrue($this->service->checkAccess( $resource, $this->teamMember ));
		$this->assertFalse($this->service->checkAccess( $resource, $this->assistant ));
	}

	public function testCreateRuleForRole() {
		$resource = 'testRule';
		$this->assertTrue($this->service->checkAccess( $resource, $this->teamMember ));
		$this->assertTrue($this->service->checkAccess( $resource, $this->assistant ));

		$rule = new AccessRule();
		$rule->setName("testName");
		$rule->setResource($resource);
		$rule->setRoles([$this->teamMemberRole]);
		$this->service->createRule($rule);

		$this->assertTrue($this->service->checkAccess( $resource, $this->teamMember ));
		$this->assertFalse($this->service->checkAccess( $resource, $this->assistant ));
	}

	public function testCreateRuleForExecutiveBoard() {
		$resource = 'testRule';
		$this->assertTrue($this->service->checkAccess( $resource, $this->executiveBoardMember ));
		$this->assertTrue($this->service->checkAccess( $resource, $this->teamMember ));

		$rule = new AccessRule();
		$rule->setName("testName");
		$rule->setResource($resource);
		$rule->setForExecutiveBoard(true);
		$this->service->createRule($rule);

		$this->assertTrue($this->service->checkAccess( $resource, $this->executiveBoardMember ));
		$this->assertFalse($this->service->checkAccess( $resource, $this->teamMember ));
	}

	public function testOldTeamMemberDoesNotHaveAccess() {
		//TODO
		$this->assertTrue(false);
	}

	public function testOldExecutiveTeamMemberDoesNotHaveAccess() {
		//TODO
		$this->assertTrue(false);
	}

	public function testInactiveUserDoesNotHaveAccess() {
		//TODO
		$this->assertTrue(false);
	}

	public function testAdminAlwaysHasAccess() {
		//TODO
		$this->assertTrue(false);
	}

	public function testAllConditionsInRuleHaveToPass() {
		//TODO
		$this->assertTrue(false);
	}

	public function testOnlyOneOfAllRulesNeedsToPass() {
		//TODO
		$this->assertTrue(false);
	}

	public function testUnhandledRuleNotificationIsCreatedIfRuleDoesNotExist() {
		//TODO
		$this->assertTrue(false);
	}

	public function testUnhandledRuleNotificationIsNotCreatedIfRuleDoesExist() {
		//TODO
		$this->assertTrue(false);
	}

	public function testUnhandledRuleNotificationIsRemovedWhenRuleIsCreated() {
		//TODO
		$this->assertTrue(false);
	}

	public function testAccessToMethodIsDenied() {
		//TODO
		$this->assertTrue(false);
	}

	public function testAccessToMethodIsSuccessful() {
		//TODO
		$this->assertTrue(false);
	}
}
