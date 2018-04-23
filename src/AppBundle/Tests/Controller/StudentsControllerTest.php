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
        $this->assertEquals(1, $crawler->filter('h1:contains("Assistenter")')->count());
        $this->assertEquals(1, $crawler->filter('p:contains("Vektorprogrammet er en studentorganisasjon som sender realfagssterke studenter")')->count());
    }
}
