<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;

class TeamApplicationControllerTest extends BaseWebTestCase
{
    public function testShowApplication()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/team/1');

        $link = $crawler->selectLink('Søk Styret!')->eq(0)->link();
        $crawler = $client->click($link);

        $this->assertEquals(1, $crawler->filter('h1:contains("Søk Styret")')->count());
    }

    public function testShowAllApplications()
    {
        $crawler = $this->adminGoTo('/kontrollpanel/team/applications/1');

        //Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h2:contains("Søknader Styret")')->count());
    }

    public function testShow()
    {
        $client = $this->createTeamLeaderClient();

        $crawler = $client->request('GET', '/kontrollpanel/team/applications/1');

        // Find a link and click it
        $link = $crawler->selectLink('Arnt Erik')->eq(0)->link();
        $crawler = $client->click($link);

        //Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h3:contains("Søknad til Styret fra ")')->count());
    }

    public function testAcceptApplication()
    {
        $clientAnonymous = static::createClient();

        $crawler = $clientAnonymous->request('GET', '/team/1');
        $this->assertTrue($clientAnonymous->getResponse()->isSuccessful());

        // Find a link
        $this->assertGreaterThan(0, $crawler->selectLink('Søk Styret!')->count());

        // Admin
        $clientAdmin = $this->createTeamLeaderClient();

        $crawler = $clientAdmin->request('GET', '/kontrollpanel/teamadmin/update/1');

        $this->assertTrue($clientAdmin->getResponse()->isSuccessful());

        $submitButton = $crawler->selectButton('Lagre');
        $form = $submitButton->form();
        $form['createTeam[acceptApplication]']->untick();
        $clientAdmin->submit($form);

        $crawler = $clientAnonymous->request('GET', '/team/1');
        $this->assertTrue($clientAnonymous->getResponse()->isSuccessful());

        // Assert that the link is gone
        $this->assertEquals(0, $crawler->selectLink('Søk Styret')->count());
    }

    public function testCreateApplication()
    {
        $clientAdmin = $this->createTeamLeaderClient();

        $crawler = $clientAdmin->request('GET', '/kontrollpanel/team/applications/1');
        $this->assertTrue($clientAdmin->getResponse()->isSuccessful());

        $applicationsBefore = $crawler->filter('tr')->count();

        // Submit an application
        // User
        $clientAnonymous = static::createClient();

        $crawler = $clientAnonymous->request('GET', '/team/application/1');
        $this->assertTrue($clientAnonymous->getResponse()->isSuccessful());

        $submitButton = $crawler->selectButton('Send');
        $form = $submitButton->form();

        $form['app_bundle_team_application_type[name]'] = 'Sondre';
        $form['app_bundle_team_application_type[email]'] = 'sondre@vektorprogrammet.no';
        $form['app_bundle_team_application_type[phone]'] = '12345678';
        $form['app_bundle_team_application_type[yearOfStudy]'] = '3. klasse';
        $form['app_bundle_team_application_type[fieldOfStudy]'] = 'MTTK';
        $form['app_bundle_team_application_type[motivationText]'] = 'Jeg vil bli nestleder! Pls.';
        $form['app_bundle_team_application_type[biography]'] = 'Made in Bergen.';
        $clientAnonymous->submit($form);

        $crawler = $clientAdmin->request('GET', '/kontrollpanel/team/applications/1');
        $this->assertTrue($clientAdmin->getResponse()->isSuccessful());

        $applicationsAfter = $crawler->filter('tr')->count();

        $this->assertEquals($applicationsBefore + 1, $applicationsAfter);
    }

    public function testDeleteApplication()
    {
        $clientAdmin = $this->createTeamLeaderClient();

        $crawler = $clientAdmin->request('GET', '/kontrollpanel/team/applications/1');
        $this->assertTrue($clientAdmin->getResponse()->isSuccessful());

        $applicationsBefore = $crawler->filter('tr')->count();

        // Delete an application

        $crawler = $clientAdmin->request('GET', '/kontrollpanel/team/applications/1');

        // Find a link and click it (Sjekk hva som skal stå for søknad nr 1)
        $deleteButton = $crawler->selectButton('Slett')->first();
        $form = $deleteButton->form();
        $clientAdmin->submit($form);

        $crawler = $clientAdmin->request('GET', '/kontrollpanel/team/applications/1');
        $this->assertTrue($clientAdmin->getResponse()->isSuccessful());

        $this->assertEquals(1, $crawler->filter('td:contains("Ingen søkere")')->count());
    }
}
