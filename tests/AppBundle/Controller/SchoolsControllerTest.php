<?php

namespace Tests\AppBundle\Controller;

use Tests\BaseWebTestCase;

class SchoolsControllerTest extends BaseWebTestCase
{
    public function testShow()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/skoler');

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct amount of data
        $this->assertEquals(1, $crawler->filter('h1:contains("Vektorassistenter i skolen")')->count());
        $this->assertEquals(1, $crawler->filter('p:contains("Vektorprogrammet er Norges største organisasjon som arbeider for å øke interessen for ")')->count());
    }
}
