<?php

namespace Tests\AppBundle\Controller;

use Tests\BaseWebTestCase;

class TeamAdminControllerTest extends BaseWebTestCase
{
    public function testCreatePosition()
    {
        // ADMIN
        $client = $this->createAdminClient();

        $crawler = $client->request('GET', '/kontrollpanel/teamadmin/opprett/stilling');

        $form = $crawler->selectButton('Lagre')->form();

        // Change the value of a field
        $form['createPosition[name]'] = 'Nestleder1';

        // submit the form
        $crawler = $client->submit($form);

        // Follow the redirect
        $crawler = $client->followRedirect();

        // Assert that we have the correct page
        $this->assertGreaterThanOrEqual(1, $crawler->filter('td:contains("Leder")')->count());
        $this->assertGreaterThanOrEqual(1, $crawler->filter('td:contains("Nestleder1")')->count());
    }

    public function testEditPosition()
    {
        // ADMIN
        $client = $this->createAdminClient();

        $crawler = $client->request('GET', '/kontrollpanel/teamadmin/rediger/stilling/2');

        $form = $crawler->selectButton('Lagre')->form();

        // Change the value of a field
        $form['createPosition[name]'] = 'test123';

        // submit the form
        $crawler = $client->submit($form);

        // Follow the redirect
        $crawler = $client->followRedirect();

        // Assert that we have the correct page
        $this->assertGreaterThanOrEqual(1, $crawler->filter('td:contains("Leder")')->count());
        $this->assertGreaterThanOrEqual(1, $crawler->filter('td:contains("test123")')->count());
        $this->assertEquals(0, $crawler->filter('td:contains("Medlem")')->count());
    }

    public function testShowPositions()
    {

        // ADMIN
        $client = $this->createAdminClient();

        $crawler = $client->request('GET', '/kontrollpanel/teamadmin/stillinger');

        // Assert that we have the correct page
        $this->assertGreaterThanOrEqual(1, $crawler->filter('td:contains("Leder")')->count());
        $this->assertGreaterThanOrEqual(1, $crawler->filter('td:contains("Medlem")')->count());
    }

    public function testCreateTeamWithNonUniqueName()
    {
        // ADMIN
        $client = $this->createAdminClient();

        $crawler = $client->request('GET', '/kontrollpanel/teamadmin/avdeling/opprett/1');

        $form = $crawler->selectButton('Lagre')->form();

        // Change the value of a field
        $form['createTeam[name]'] = 'IT';
        $form['createTeam[email]'] = 'hackerclub@vektorprogrammet.no';

        // submit the form
        $client->submit($form);

        // Assert not redirected
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testCreateTeamForDepartment()
    {
        // ADMIN
        $client = $this->createAdminClient();

        $crawler = $client->request('GET', '/kontrollpanel/teamadmin/avdeling/opprett/1');

        $form = $crawler->selectButton('Lagre')->form();

        // Change the value of a field
        $form['createTeam[name]'] = 'testteam1';
        $form['createTeam[email]'] = 'testteam1@vektorprogrammet.no';

        // submit the form
        $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertEquals(1, $crawler->filter('td:contains("testteam1@vektorprogrammet.no")')->count());
    }

    public function testUpdateTeam()
    {

        // ADMIN
        $client = $this->createAdminClient();

        $crawler = $client->request('GET', '/kontrollpanel/teamadmin/update/1');

        $form = $crawler->selectButton('Lagre')->form();

        // Change the value of a field
        $form['createTeam[name]'] = 'testteam2';
        $form['createTeam[email]'] = 'testteam2@vektorprogrammet.no';

        // submit the form
        $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertEquals(1, $crawler->filter('td:contains("testteam2@vektorprogrammet.no")')->count());
        $this->assertEquals(0, $crawler->filter('td:contains("Styret")')->count());
    }

    public function testAddUserToTeam()
    {
        // ADMIN
        $client = $this->createAdminClient();

        $crawler = $client->request('GET', '/kontrollpanel/teamadmin/team/nytt_medlem/1');

        $form = $crawler->selectButton('Legg til')->form();

        // Change the value of a field
        $form['createTeamMembership[user]']->select(1);
        $form['createTeamMembership[position]']->select(1);
        $form['createTeamMembership[startSemester]']->select(1);

        $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertGreaterThanOrEqual(1, $crawler->filter('td:contains("Petter Johansen")')->count());
    }

    public function testShowTeamsByDepartment()
    {

        // ADMIN
        $client = $this->createAdminClient();

        $crawler = $client->request('GET', '/kontrollpanel/team/avdeling/1');

        // Check the count for the different variables
        $this->assertEquals(1, $crawler->filter('a:contains("Styret")')->count());
        $this->assertEquals(1, $crawler->filter('a:contains("IT")')->count());
    }

    public function testShowSpecificTeam()
    {
        // TEAM
        $client = $this->createTeamMemberClient();

        $crawler = $client->request('GET', '/kontrollpanel/teamadmin/team/1');

        // Check the count for the different variables
        $this->assertEquals(1, $crawler->filter('td:contains("Petter Johansen")')->count());
        $this->assertEquals(2, $crawler->filter('td:contains("Thomas Alm")')->count());
    }

    public function testShow()
    {
        // TEAM
        $client = $this->createTeamMemberClient();

        $crawler = $client->request('GET', '/kontrollpanel/team/avdeling');

        $this->assertEquals(1, $crawler->filter('a:contains("Styret")')->count());
        $this->assertEquals(1, $crawler->filter('a:contains("IT")')->count());
    }
}
