<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TeamApplicationControllerTest extends WebTestCase
{
    public function testShowApplication(){

        $client = static::createClient();

        $crawler = $client->request('GET', '/team/1');

        // Find a link and click it
        $link = $crawler->selectLink('Søk Hovedstyret')->eq(0)->link();
        $crawler = $client->click($link);

        //Assert that we have the correct page (Spør Kristoffer om contains, må det være hele innholdet?)
        $this->assertEquals(1, $crawler->filter('h1:contains("Søk Hovedstyret")')->count());
        //Form checks?

    }

    public function testShowAllApplications(){
        // ADMIN
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'petjo', // Skal det stå admin her?
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/teamadmin/team/1');

        // Find a link and click it (Sjekk hva som skal stå for søknad nr 1)
        $link = $crawler->selectLink('Se søkere')->eq(0)->link();
        $crawler = $client->click($link);

        //Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h3:contains("Søknader til Hovedstyret")')->count());

        // USER
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'assistent',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/team/applications/1');

        // Assert that the response is a redirect
        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        // TEAM
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'team',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/team/applications/1');

        // Assert that the response is a redirect
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testShow(){

        // ADMIN
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'petjo', // Skal det stå admin her?
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/team/applications/1');

        // Find a link and click it (Sjekk hva som skal stå for søknad nr 1)
        $link = $crawler->selectLink('Søknad nr 1')->eq(0)->link();
        $crawler = $client->click($link);

        //Assert that we have the correct page (Spør Kristoffer om contains, må det være hele innholdet?)
        $this->assertEquals(1, $crawler->filter('h3:contains("Søknad til Hovedstyret fra ")')->count());

        // USER
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'assistent',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/team/application/1');

        // Assert that the response is a redirect
        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        // TEAM
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'team',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/team/application/1');

        // Assert that the response is a redirect
        $this->assertEquals(403, $client->getResponse()->getStatusCode());

    }
}
