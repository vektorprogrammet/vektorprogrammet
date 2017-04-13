<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;

class ExecutiveBoardControllerTest extends BaseWebTestCase
{
    public function testUpdateExecutiveBoard()
    {
        // ADMIN
        $client = $this->createTeamLeaderClient();

        $crawler = $this->goTo('/kontrollpanel/hovedstyret', $client);

        // Find a link and click it
        $link = $crawler->selectLink('Rediger')->link();
        $crawler = $client->click($link);

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Oppdater Hovedstyret")')->count());

        $form = $crawler->selectButton('Lagre')->form();

        // Change the value of a field
        $form['createExecutiveBoard[name]'] = 'nyttStyre';

        // submit the form
        $client->submit($form);

        // Follow the redirect
        $crawler = $client->followRedirect();

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("nyttStyre")')->count());
        $this->assertEquals(0, $crawler->filter('h1:contains("Hovedstyret")')->count());
        \TestDataManager::restoreDatabase();
    }

    public function testAddUserToBoard()
    {
        $client = $this->createTeamLeaderClient();

        $crawler = $this->goTo('/kontrollpanel/hovedstyret', $client);

        // Find a link and click it
        $link = $crawler->selectLink('Legg til ny bruker')->eq(0)->link();
        $crawler = $client->click($link);

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Legg til bruker i Hovedstyret")')->count());

        $form = $crawler->selectButton('Lagre')->form();

        // Change the value of a field
        $form['createExecutiveBoardMember[user]']->select(1);
        $form['createExecutiveBoardMember[position]'] = 'Leder';

        // submit the form
        $client->submit($form);

        // Follow the redirect
        $crawler = $client->followRedirect();

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Hovedstyret")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Petter")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Johansen")')->count());

        $client = $this->createAssistantClient();

        $client->request('GET', '/kontrollpanel/hovedstyret/nytt_medlem');

        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        $client = $this->createTeamMemberClient();

        $client->request('GET', '/kontrollpanel/hovedstyret/nytt_medlem');

        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        \TestDataManager::restoreDatabase();
    }

    public function testShowAdmin()
    {
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/hovedstyret');

        // Check the count for the different variables
        $this->assertEquals(1, $crawler->filter('h1:contains("Hovedstyret")')->count());

        $client = $this->createTeamMemberClient();

        $client->request('GET', '/kontrollpanel/hovedstyret');

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testShow()
    {
        $crawler = $this->goTo('/hovedstyret');

        $this->assertEquals(1, $crawler->filter('h1:contains("Hovedstyret")')->count());
    }
}
