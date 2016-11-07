<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NewsletterControllerTest extends WebTestCase
{
    public function testShowWithActiveAdmission()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/opptak/avdeling/1');

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct amount of data
        $this->assertEquals(0, $crawler->filter('p:contains("Du kan melde deg på ")')->count());

    }
    public function testShowWithoutActiveAdmission()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/opptak/avdeling/2');

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct amount of data
        $this->assertEquals(0, $crawler->filter('p:contains("Du kan melde deg på ")')->count());
        $this->assertEquals(1, $crawler->filter('h4:contains("har ikke aktiv søkeperiode")')->count());

    }
    public function testShowWithActiveNewsletter()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/opptak/avdeling/3');

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct amount of data
        $this->assertEquals(1, $crawler->filter('p:contains("Du kan melde deg på ")')->count());
        $this->assertEquals(1, $crawler->filter('h4:contains("har ikke aktiv søkeperiode")')->count());
    }
    public function testNewsletterLinkInControlPanel()
    {
    $client = static::createClient(array(
        'PHP_AUTH_USER' => 'admin',
        'PHP_AUTH_PW' => '1234',
    ));
    dump($client->getResponse()->isRedirection());

    $crawler = $client->request('GET', '/kontrollpanel');

    $this->assertTrue($client->getResponse()->isSuccessful());

    // Assert that we have the correct amount of data
    $this->assertGreaterThanOrEqual(3, $crawler->filter('a:contains("Nyhetsbrev")')->count());
    }
    public function testShowSpecificNewsletter()
    {
        $client = static::createClient(array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => '1234',
        ));

        $client->followRedirect();
        $crawler = $client->request('GET', '/kontrollpanel/nyhetsbrev/1');

        $this->assertTrue($client->getResponse()->isSuccessful());

        // Assert that we have the correct amount of data
        $this->assertEquals(1, $crawler->filter('a:contains("Rediger nyhetsbrevet")')->count());
        $this->assertEquals(1, $crawler->filter('button:contains("Slett nyhetsbrevet")')->count());
        $this->assertGreaterThanOrEqual(2, $crawler->filter('button:contains("Slett")')->count());

        $client = static::createClient(array(
            'PHP_AUTH_USER' => 'team',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/nyhetsbrev/1');

        $this->assertTrue($client->getResponse()->isSuccessful());

        // Assert that we have the correct amount of data
        $this->assertEquals(0, $crawler->filter('a:contains("Rediger nyhetsbrevet")')->count());
        $this->assertEquals(0, $crawler->filter('button:contains("Slett nyhetsbrevet")')->count());
        $this->assertEquals(0, $crawler->filter('button:contains("Slett")')->count());
    }
}
