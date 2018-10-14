<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;

class UserAdminControllerTest extends BaseWebTestCase
{
    public function testCreateUser()
    {

        // TEAM
        $client = $this->createTeamLeaderClient();

        $crawler = $client->request('GET', '/kontrollpanel/brukeradmin/opprett/1');

        $form = $crawler->selectButton('Opprett')->form();

        // Change the value of a field
        $form['createUser[firstName]'] = 'fornavn2';
        $form['createUser[lastName]'] = 'etternavn2';
        $form['createUser[gender]']->select(0);
        $form['createUser[phone]'] = '22288222';
        $form['createUser[email]'] = 'fornavn2@mail.com';
        $form['createUser[fieldOfStudy]']->select(1);

        // submit the form
        $crawler = $client->submit($form);

        // Assert that the response is the correct redirect
        $this->assertTrue($client->getResponse()->isRedirection());

        // Follow the redirect
        $crawler = $client->followRedirect();
    }

    public function testShowUsersByDepartment()
    {

        // ADMIN
        $client = $this->createAdminClient();

        $crawler = $client->request('GET', '/kontrollpanel/brukeradmin/avdeling/1');

        // Assert that we have the correct amount of data
        $this->assertEquals(1, $crawler->filter('h2:contains("Brukere")')->count());
        $this->assertGreaterThanOrEqual(1, $crawler->filter('td:contains("Reidun")')->count());
        $this->assertGreaterThanOrEqual(1, $crawler->filter('td:contains("Persdatter Ødegaard")')->count());
        $this->assertGreaterThanOrEqual(1, $crawler->filter('td:contains("Siri")')->count());
        $this->assertGreaterThanOrEqual(1, $crawler->filter('td:contains("Brenna Eskeland")')->count());

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testShow()
    {

        // TEAM
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'idaan',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/brukeradmin');

        // Assert that we have the correct amount of data
        $this->assertEquals(1, $crawler->filter('h2:contains("Brukere")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Reidun")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Persdatter Ødegaard")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Siri")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Brenna Eskeland")')->count());

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /*
    Requires JQuery interaction, Symfony2 does not support that

    Phpunit was designed to test the PHP language, have to use another tool to test these.

    public function testDeleteUserById() {}

    */
}
