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

		$countBefore = $this->countTableRows("/kontrollpanel/admin/accessrules");
		$client = $this->createAdminClient();
		$crawler = $this->goTo("/kontrollpanel/admin/accessrules/create", $client);
		$form = $crawler->selectButton("Save")->form();

		$form["routing_access_rule[name]"] = "Test Rule";
		$form["routing_access_rule[resource]"] = "assistants";
		$client->submit($form);

		$countAfter = $this->countTableRows("/kontrollpanel/admin/accessrules");

		$this->assertGreaterThan($countBefore, $countAfter);
	}
}
