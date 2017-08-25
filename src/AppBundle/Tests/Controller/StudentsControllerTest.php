<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;

class StudentsControllerTest extends BaseWebTestCase
{
    public function testShow()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/studenter');

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct amount of data
        $this->assertEquals(1, $crawler->filter('h1:contains("For studenter")')->count());
        $this->assertEquals(1, $crawler->filter('h1:contains("Kontakt")')->count());
        $this->assertEquals(1, $crawler->filter('strong:contains("Generelle henvendelser rettes til:")')->count());
        $this->assertEquals(1, $crawler->filter('p:contains("studenter@vektorprogrammet.no")')->count());
    }
}
