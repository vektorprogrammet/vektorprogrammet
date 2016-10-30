<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AboutVektorControllerTest extends WebTestCase
{
    public function testShow()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/omvektor');

        // Assert that we have the correct amount of data
        $this->assertEquals(1, $crawler->filter('h1:contains("Om Vektorprogrammet")')->count());
        $this->assertEquals(1, $crawler->filter('h1:contains("Mål og verdier")')->count());
        $this->assertEquals(1, $crawler->filter('h1:contains("Historie")')->count());

        $this->assertEquals(1, $crawler->filter('strong:contains("Vektorprogrammet arbeider for å øke interessen for matematikk og realfag blant elever i grunnskolen. ")')->count());
        $this->assertEquals(1, $crawler->filter('li:contains("Vektorprogrammet ønsker å øke matematikkforståelsen blant elever i grunnskolen.")')->count());
        $this->assertEquals(1, $crawler->filter('p:contains("Vektorprogrammet er et initiativ som ble startet av linjeforeningen Nabla i september 2010")')->count());

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testShowFaq()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/faq');

        // Assert that we have the correct amount of data
        $this->assertEquals(1, $crawler->filter('h1:contains("Ofte stilte spørsmål")')->count());

    }
}
