<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BoardAndTeamControllerTest extends WebTestCase
{
    public function testShow()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/styretogteam');

        // Assert that we have the correct page
        $this->assertContains('Styret og team', $client->getResponse()->getContent());

        // Assert that we have the correct team(s)
        $this->assertContains('Hovedstyret', $client->getResponse()->getContent());
        $this->assertContains('IT', $client->getResponse()->getContent());

        // Check the count for the parameters
        $this->assertEquals(1, $crawler->filter('html:contains("Hovedstyret")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("IT")')->count());

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
