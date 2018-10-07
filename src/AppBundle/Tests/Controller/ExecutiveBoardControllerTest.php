<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;

class ExecutiveBoardControllerTest extends BaseWebTestCase
{
    public function testUpdateExecutiveBoard()
    {
        $client = $this->createTeamLeaderClient();

        $crawler = $this->goTo('/kontrollpanel/hovedstyret/oppdater', $client);

        $form = $crawler->selectButton('Lagre')->form();

        // Change the value of a field
        $form['createExecutiveBoard[name]'] = 'nyttStyre';

        // submit the form
        $client->submit($form);

        // Follow the redirect
        $crawler = $client->followRedirect();

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h2:contains("nyttStyre")')->count());
        $this->assertEquals(0, $crawler->filter('h2:contains("Hovedstyret")')->count());
    }

    public function testAddUserToBoard()
    {
        $client = $this->createTeamLeaderClient();

        $crawler = $this->goTo('/kontrollpanel/hovedstyret', $client);

        $numActiveRowsBefore = $crawler->filter('#activeMemberTable tr')->count(); // Includes header row

        // Find a link and click it
        $link = $crawler->selectLink('Trondheim')->eq(0)->link();
        $crawler = $client->click($link);

        // Assert that we have the correct page
        $this->assertGreaterThanOrEqual(1, $crawler->filter('div:contains("Legg til hovedstyremedlem")')->count());

        $form = $crawler->selectButton('Lagre')->form();

        // Change the value of a field
        $form['createExecutiveBoardMembership[user]']->select(1);
        $form['createExecutiveBoardMembership[positionName]'] = 'Leder';
        $form['createExecutiveBoardMembership[startSemester]']->select(1);

        // submit the form
        $client->submit($form);

        // Follow the redirect
        $crawler = $client->followRedirect();

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h2:contains("Hovedstyret")')->count());
        $this->assertGreaterThanOrEqual(1, $crawler->filter('td:contains("Petter Johansen")')->count());

        // Assert that a user was added to the active table
        $this->assertEquals($numActiveRowsBefore + 1, $crawler->filter('#activeMemberTable tr')->count());
    }

    public function testEditBoardMembership()
    {
        // Select and populate form
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/hovedstyret/rediger_medlem/1');
        $form = $crawler->selectButton('Lagre')->form();
        $form['createExecutiveBoardMembership[positionName]'] = 'Testposisjon';

        // Submit form
        $client = $this->createTeamLeaderClient();
        $client->submit($form);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        // Check that the membership was edited
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/hovedstyret');
        $this->assertEquals(1, $crawler->filter('tr:contains("Testposisjon")')->count());
    }

    public function testDeleteBoardMembership()
    {
        // Count rows before
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/hovedstyret');
        $numRowsBefore = $crawler->filter('tr')->count(); // Includes header rows

        // Delete a membership
        $client = $this->createAdminClient();
        $this->createAdminClient()->request('POST', '/kontrollpanel/hovedstyret/slett/bruker/2');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        // Assert one less row after deletion
        $crawler = $client->followRedirect();
        $this->assertEquals($numRowsBefore - 1, $crawler->filter('tr')->count());
    }

    public function testShowAdmin()
    {
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/hovedstyret');

        // Check the count for the different variables
        $this->assertEquals(1, $crawler->filter('h2:contains("Hovedstyret")')->count());
    }

    public function testShow()
    {
        $crawler = $this->goTo('/hovedstyret');

        $this->assertEquals(1, $crawler->filter('h1:contains("Hovedstyret")')->count());
    }
}
