<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SchoolsControllerTest extends WebTestCase
{
    public function testShow()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/skoler');

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct amount of data
        $this->assertEquals(1, $crawler->filter('h1:contains("For skoler")')->count());
        $this->assertEquals(1, $crawler->filter('h1:contains("Kontakt")')->count());
        $this->assertEquals(1, $crawler->filter('strong:contains("Generelle henvendelser rettes til:")')->count());
        $this->assertEquals(1, $crawler->filter('p:contains("kunne drive Vektorprogrammet er det helt essensielt med samarbeidsavtaler med skoler...")')->count());
    }
}
