<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;

class ParticipantHistoryControllerTest extends BaseWebTestCase
{
    public function testIndex()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'petjo',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/deltakerhistorikk');

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Deltakerhistorie")')->count());
        $this->assertEquals(1, $crawler->filter('h3:contains("Arbeidshistorie")')->count());
        $this->assertEquals(1, $crawler->filter('h3:contains("Assistent historie")')->count());

        // Assert that we have the correct data
        $this->assertContains('Petter Johansen', $client->getResponse()->getContent());
        $this->assertContains('petter@stud.ntnu.no', $client->getResponse()->getContent());
        $this->assertContains('Hovedstyret', $client->getResponse()->getContent());
        $this->assertContains('NTNU', $client->getResponse()->getContent());
        $this->assertContains('Bolk 2', $client->getResponse()->getContent());
        $this->assertContains('Onsdag', $client->getResponse()->getContent());
        $this->assertContains('Gimse', $client->getResponse()->getContent());

        // Check the count for the different variables
        $this->assertEquals(2, $crawler->filter('a:contains("Petter Johansen")')->count());
        $this->assertEquals(2, $crawler->filter('td:contains("petter@stud.ntnu.no")')->count());
        $this->assertEquals(2, $crawler->filter('td:contains("Bolk 2")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Onsdag")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Gimse")')->count());

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
