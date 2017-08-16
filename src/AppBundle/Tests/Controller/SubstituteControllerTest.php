<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;

class SubstituteControllerTest extends BaseWebTestCase
{
    public function testShow()
    {
        // Team leader
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/vikar');

        // Assert that we have the edit/delete buttons as admin
        $this->assertGreaterThanOrEqual(1, $crawler->filter('button:contains("Slett")')->count());
        $this->assertGreaterThanOrEqual(1, $crawler->filter('button:contains("Rediger")')->count());

        // Team member
        $crawler = $this->teamMemberGoTo('/kontrollpanel/vikar');

        // Assert that we don't have the edit/delete buttons as team
        $this->assertEquals(0, $crawler->filter('a:contains("Slett")')->count());
        $this->assertEquals(0, $crawler->filter('a:contains("Rediger")')->count());
    }

    public function testShowSubstitutesByDepartment()
    {
        // Team leader
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/vikar/semester/2');

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Vikarer")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Team")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Johansen")')->count());

        // Assert that we have the edit/delete buttons as admin
        $this->assertEquals(1, $crawler->filter('button:contains("Slett")')->count());
        $this->assertEquals(1, $crawler->filter('button:contains("Rediger")')->count());
    }

	public function testIllegalCreateMethod()
	{
		$client = self::createTeamLeaderClient();

		$client->request('GET', '/kontrollpanel/vikar/opprett/4');

		$this->assertEquals(405, $client->getResponse()->getStatusCode());
	}

	public function testCreate()
	{
		$subCountBefore = $this->countTableRows('/kontrollpanel/vikar');

		$client = self::createTeamLeaderClient();
		$client->request('POST', '/kontrollpanel/vikar/opprett/4');

		$subCountAfter = $this->countTableRows('/kontrollpanel/vikar');

		$this->assertEquals(1, $subCountAfter - $subCountBefore);
	}

    public function testEdit()
    {
        // Team leader
        $client = self::createTeamLeaderClient();

        $crawler = $this->goTo('/kontrollpanel/vikar/rediger/1', $client);

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Rediger vikar")')->count());

        // Find the form
        $form = $crawler->selectButton('Oppdater')->form();

        // Fill in the form
        $form['modifySubstitute[user][phone]'] = '95999999';

        // Submit the form
        $client->submit($form);

        // Follow the redirect
        $crawler = $client->followRedirect();

        // Assert that we have the correct page with the correct info (from the submitted form)
        $this->assertEquals(1, $crawler->filter('h1:contains("Vikarer")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("95999999")')->count());

        // Team user
        $client = self::createTeamMemberClient();

        $client->request('GET', '/kontrollpanel/vikar/rediger/4');

        // Assert that the page response status code is 403 access denied
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }
}
