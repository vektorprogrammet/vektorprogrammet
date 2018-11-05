<?php


namespace Tests\AppBundle\Service;


use AppBundle\Entity\AccessRule;
use AppBundle\Entity\Repository\UnhandledAccessRuleRepository;
use AppBundle\Role\Roles;
use AppBundle\Service\AccessControlService;
use Tests\BaseKernelTestCase;

class AccessControlTest extends BaseKernelTestCase {
	/**
	 * @var AccessControlService $service
	 */
	private $service;
	private $assistant;
	private $teamMember;
	private $inactiveTeamMember;
	private $team;
	private $teamMemberRole;
	private $teamLeaderRole;
	private $executiveBoardMember;
	private $inactiveExecutiveBoardMember;
	private $inactiveUser;
	private $admin;
	private $teamMemberAndExecutiveBoardMember;

	/**
	 * @var UnhandledAccessRuleRepository $unhandledRepo
	 */
	private $unhandledRepo;

	protected function setUp()
	{
		$kernel = $this->createKernel();
		$kernel->boot();

		$this->service = $kernel->getContainer()->get('app.access_control');
		$em = $kernel->getContainer()->get('doctrine')->getManager();
		$this->unhandledRepo = $em->getRepository('AppBundle:UnhandledAccessRule');
		$this->inactiveUser = $em->getRepository('AppBundle:User')->findUserByEmail('inactive@mail.com');
		$this->assistant = $em->getRepository('AppBundle:User')->findUserByEmail('assistant@gmail.com');
		$this->teamMember = $em->getRepository('AppBundle:User')->findUserByEmail('marte@mail.no');
		$this->teamMemberAndExecutiveBoardMember = $em->getRepository('AppBundle:User')->findUserByEmail('sortland@mail.com');
		$this->inactiveTeamMember = $em->getRepository('AppBundle:User')->findUserByEmail('aai@b.c');
		$this->executiveBoardMember = $em->getRepository('AppBundle:User')->findUserByEmail('angela@mail.no');
		$this->inactiveExecutiveBoardMember = $em->getRepository('AppBundle:User')->findUserByEmail('jan-per-gustavio@gmail.com');
		$this->admin = $em->getRepository('AppBundle:User')->findUserByEmail('admin@gmail.com');
		$this->team = $em->getRepository('AppBundle:Team')->find(1);
		$this->teamMemberRole = $em->getRepository('AppBundle:Role')->findByRoleName(Roles::TEAM_MEMBER);
		$this->teamLeaderRole = $em->getRepository('AppBundle:Role')->findByRoleName(Roles::TEAM_LEADER);
	}

	public function testHasAccessWhenRuleDoesNotExist() {
		$access = $this->service->checkAccess( 'nonexisting', $this->assistant );
		$this->assertTrue( $access );
	}

	public function testCreateEmptyRule() {
		$resource = 'testRule';
		$this->assertTrue( $this->service->checkAccess( $resource, $this->assistant ) );
		$this->assertTrue( $this->service->checkAccess( $resource, null ) );

		$rule = new AccessRule();
		$rule->setName("testName");
		$rule->setResource($resource);
		$this->service->createRule($rule);

		$this->assertTrue( $this->service->checkAccess( $resource, $this->assistant ) );
		$this->assertTrue( $this->service->checkAccess( $resource, null ) );
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
		$rule->setRoles([$this->teamMemberRole, $this->teamLeaderRole]);
		$rule->setForExecutiveBoard(true);
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
		$resource = 'testRule';
		$this->assertTrue($this->service->checkAccess( $resource, $this->teamMember ));
		$this->assertTrue($this->service->checkAccess( $resource, $this->inactiveTeamMember ));

		$rule = new AccessRule();
		$rule->setName("testName");
		$rule->setResource($resource);
		$rule->setTeams([$this->team]);
		$this->service->createRule($rule);

		$this->assertTrue($this->service->checkAccess( $resource, $this->teamMember ));
		$this->assertFalse($this->service->checkAccess( $resource, $this->inactiveTeamMember ));
	}

	public function testOldExecutiveTeamMemberDoesNotHaveAccess() {
		$resource = 'testRule';
		$this->assertTrue($this->service->checkAccess( $resource, $this->executiveBoardMember ));
		$this->assertTrue($this->service->checkAccess( $resource, $this->inactiveExecutiveBoardMember ));

		$rule = new AccessRule();
		$rule->setName("testName");
		$rule->setResource($resource);
		$rule->setForExecutiveBoard(true);
		$this->service->createRule($rule);

		$this->assertTrue($this->service->checkAccess( $resource, $this->executiveBoardMember ));
		$this->assertFalse($this->service->checkAccess( $resource, $this->inactiveExecutiveBoardMember ));
	}

	public function testInactiveUserDoesNotHaveAccess() {
		$resource = 'testRule';
		$this->assertTrue($this->service->checkAccess( $resource, $this->inactiveUser ));
		$this->assertTrue($this->service->checkAccess( $resource, $this->assistant ));

		$rule = new AccessRule();
		$rule->setName("testName");
		$rule->setResource($resource);
		$rule->setUsers([$this->assistant, $this->inactiveUser]);
		$this->service->createRule($rule);

		$this->assertTrue($this->service->checkAccess( $resource, $this->assistant ));
		$this->assertFalse($this->service->checkAccess( $resource, $this->inactiveUser ));
	}

	public function testAdminAlwaysHasAccess() {
		$resource = 'testRule';
		$this->assertTrue($this->service->checkAccess( $resource, $this->assistant ));
		$this->assertTrue($this->service->checkAccess( $resource, $this->admin ));

		$rule = new AccessRule();
		$rule->setName("testName");
		$rule->setResource($resource);
		$rule->setUsers([$this->assistant]);
		$this->service->createRule($rule);

		$this->assertTrue($this->service->checkAccess( $resource, $this->assistant ));
		$this->assertTrue($this->service->checkAccess( $resource, $this->admin ));
	}

	public function testAllConditionsInRuleHaveToPass() {
		$resource = 'testRule';
		$this->assertTrue($this->service->checkAccess( $resource, $this->teamMemberAndExecutiveBoardMember ));
		$this->assertTrue($this->service->checkAccess( $resource, $this->assistant ));

		$rule = new AccessRule();
		$rule->setName("testName");
		$rule->setResource($resource);
		$rule->setUsers([$this->assistant, $this->teamMemberAndExecutiveBoardMember]);
		$rule->setTeams([$this->team]);
		$rule->setForExecutiveBoard(true);
		$this->service->createRule($rule);

		$this->assertTrue($this->service->checkAccess( $resource, $this->teamMemberAndExecutiveBoardMember ));
		$this->assertFalse($this->service->checkAccess( $resource, $this->assistant ));
	}

	public function testOnlyOneOfAllRulesNeedsToPass() {
		$resource = 'testRule';
		$this->assertTrue($this->service->checkAccess( $resource, $this->teamMemberAndExecutiveBoardMember ));
		$this->assertTrue($this->service->checkAccess( $resource, $this->assistant ));

		$rule = new AccessRule();
		$rule->setName("testName");
		$rule->setResource($resource);
		$rule->setUsers([$this->assistant, $this->teamMemberAndExecutiveBoardMember]);
		$rule->setTeams([$this->team]);
		$rule->setForExecutiveBoard(true);
		$this->service->createRule($rule);

		$rule2 = new AccessRule();
		$rule2->setName("testName2");
		$rule2->setResource($resource);
		$rule2->setUsers([$this->assistant, $this->teamMemberAndExecutiveBoardMember]);
		$this->service->createRule($rule2);

		$this->assertTrue($this->service->checkAccess( $resource, $this->teamMemberAndExecutiveBoardMember ));
		$this->assertTrue($this->service->checkAccess( $resource, $this->assistant ));
	}

	public function testAccessToMultipleRules() {
		$resource = 'testRule';
		$resource2 = 'testRule2';
		$this->assertTrue($this->service->checkAccess( [$resource, $resource2], $this->teamMemberAndExecutiveBoardMember ));
		$this->assertTrue($this->service->checkAccess( [$resource, $resource2], $this->assistant ));

		$rule = new AccessRule();
		$rule->setName("testName");
		$rule->setResource($resource);
		$rule->setUsers([$this->assistant, $this->teamMemberAndExecutiveBoardMember]);
		$rule->setTeams([$this->team]);
		$rule->setForExecutiveBoard(true);
		$this->service->createRule($rule);

		$rule2 = new AccessRule();
		$rule2->setName("testName2");
		$rule2->setResource($resource2);
		$rule2->setUsers([$this->assistant, $this->teamMemberAndExecutiveBoardMember]);
		$this->service->createRule($rule2);

		$this->assertTrue($this->service->checkAccess( [$resource, $resource2], $this->teamMemberAndExecutiveBoardMember ));
		$this->assertFalse($this->service->checkAccess( [$resource, $resource2], $this->assistant ));
	}

	public function testAccessToMethod() {
		$resource = 'testRule';
		$this->assertTrue($this->service->checkAccess( [$resource => 'POST'], $this->assistant ));
		$this->assertTrue($this->service->checkAccess( [$resource => 'GET'], $this->teamMemberAndExecutiveBoardMember ));

		$rule1 = new AccessRule();
		$rule1->setName("testName");
		$rule1->setResource($resource);
		$rule1->setMethod('GET');
		$rule1->setUsers([$this->assistant]);
		$this->service->createRule($rule1);

		$rule2 = new AccessRule();
		$rule2->setName("testName2");
		$rule2->setResource($resource);
		$rule2->setMethod('POST');
		$rule2->setUsers([$this->teamMemberAndExecutiveBoardMember]);
		$this->service->createRule($rule2);

		$this->assertTrue($this->service->checkAccess( [$resource => 'GET'], $this->assistant ));
		$this->assertFalse($this->service->checkAccess( [$resource => 'POST'], $this->assistant ));

		$this->assertTrue($this->service->checkAccess( [$resource => 'POST'], $this->teamMemberAndExecutiveBoardMember ));
		$this->assertFalse($this->service->checkAccess( [$resource => 'GET'], $this->teamMemberAndExecutiveBoardMember ));
	}

	public function testUnhandledRuleNotificationIsCreatedIfRuleDoesNotExist() {
		$resource = 'testRule';
		$count = $this->countUnhandled($resource);
		$this->assertEquals(0, $count);

		$this->service->checkAccess($resource);

		$count = $this->countUnhandled($resource);
		$this->assertEquals(1, $count);
	}

	public function testUnhandledRuleNotificationIsNotCreatedIfRuleDoesExist() {
		$resource = 'testRule';

		$rule = new AccessRule();
		$rule->setName("testName");
		$rule->setResource($resource);
		$this->service->createRule($rule);

		$this->service->checkAccess($resource);

		$count = $this->countUnhandled($resource);
		$this->assertEquals(0, $count);
	}

	public function testUnhandledRuleNotificationIsRemovedWhenRuleIsCreated() {
		$resource = 'testRule';

		$this->service->checkAccess($resource);

		$count = $this->countUnhandled($resource);
		$this->assertEquals(1, $count);

		$rule = new AccessRule();
		$rule->setName("testName");
		$rule->setResource($resource);
		$this->service->createRule($rule);

		$count = $this->countUnhandled($resource);
		$this->assertEquals(0, $count);
	}


	private function countUnhandled(string $resource, $method = 'GET') : int {
		return count($this->unhandledRepo->findByResourceAndMethod($resource, $method));
	}
}
