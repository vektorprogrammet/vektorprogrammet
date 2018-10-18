<?php


namespace AppBundle\Controller;


use Tests\BaseWebTestCase;

class AccessRuleControllerTest extends BaseWebTestCase {

	protected function setUp() {
		parent::setUp();
		$this->adminGoTo("/kontrollpanel/admin/accessrules"); // Initialize unhandled rules
	}

	public function testCreateCustomAccessRule() {
		$countBefore = $this->countTableRows("/kontrollpanel/admin/accessrules");
		$client = $this->createAdminClient();
		$crawler = $this->goTo("/kontrollpanel/admin/accessrules/create", $client);
		$form = $crawler->selectButton("Save")->form();

		$form["access_rule[name]"] = "Test Rule";
		$form["access_rule[resource]"] = "resource";
		$client->submit($form);

		$countAfter = $this->countTableRows("/kontrollpanel/admin/accessrules");

		$this->assertGreaterThan($countBefore, $countAfter);
	}

	public function testCreateRoutingAccessRule() {
		$anonClient = $this->createAnonymousClient();

		$anonClient->request('GET', '/assistenter');
		$this->assertEquals(200, $anonClient->getResponse()->getStatusCode());

		$crawler = $this->anonymousGoTo("/");
		$forbiddenLinksBefore = $crawler->filter('a[href="/assistenter"]');

		$this->assertGreaterThan(0, $forbiddenLinksBefore->count());

		$countBefore = $this->countTableRows("/kontrollpanel/admin/accessrules");
		$client = $this->createAdminClient();
		$crawler = $this->goTo("/kontrollpanel/admin/accessrules/routing/create", $client);
		$form = $crawler->selectButton("Save")->form();

		$form["routing_access_rule[name]"] = "Test Rule";
		$form["routing_access_rule[resource]"] = "assistants";
		$form["routing_access_rule[roles][0]"]->tick();
		$client->submit($form);

		$countAfter = $this->countTableRows("/kontrollpanel/admin/accessrules");

		$this->assertGreaterThan($countBefore, $countAfter);

		$anonClient->request('GET', '/assistenter');
		$this->assertEquals(403, $anonClient->getResponse()->getStatusCode());

		$crawler = $this->anonymousGoTo("/");
		$forbiddenLinksAfter = $crawler->filter('a[href="/assistenter"]');

		$this->assertEquals(0, $forbiddenLinksAfter->count());
	}

	public function testUnhandledRulesAreCreated() {
		$crawler = $this->adminGoTo('/kontrollpanel/admin/accessrules');
		$unhandledBefore = $crawler->filter('#unhandledModal tr')->count();

		$this->adminGoTo('/kontrollpanel/opptak/nye');

		$crawler = $this->adminGoTo('/kontrollpanel/admin/accessrules');
		$unhandledAfter = $crawler->filter('#unhandledModal tr')->count();

		$this->assertGreaterThan($unhandledBefore, $unhandledAfter);
	}

	public function testUnhandledRulesAreDeletedWhenRuleIsCreated() {
		$crawler = $this->adminGoTo('/kontrollpanel/admin/accessrules');
		$unhandledBefore = $crawler->filter('#unhandledModal tr')->count();

		$crawler = $this->adminGoTo("/kontrollpanel/admin/accessrules/routing/create");
		$form = $crawler->selectButton("Save")->form();

		$form["routing_access_rule[name]"] = "Test Rule";
		$form["routing_access_rule[resource]"] = "control_panel";
		$form["routing_access_rule[roles][0]"]->tick();

		$client = $this->createAdminClient();
		$client->submit($form);

		$crawler = $this->adminGoTo('/kontrollpanel/admin/accessrules');
		$unhandledAfter = $crawler->filter('#unhandledModal tr')->count();

		$this->assertEquals($unhandledBefore - 1, $unhandledAfter);
	}
}
