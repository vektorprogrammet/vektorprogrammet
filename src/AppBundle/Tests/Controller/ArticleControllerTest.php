<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;

class ArticleControllerTest extends BaseWebTestCase
{
    public function testShow()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/nyheter');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h5:contains("VEKAS VEKTOR­ASSISTENT! MØT BEATE!")')->count());
        $this->assertEquals(1, $crawler->filter('p:contains("Filter:")')->count());
    }

    public function testShowFilter()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/nyheter/ntnu');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('p:contains("Filter:")')->count());
        $this->assertEquals(1, $crawler->filter('a.department-filter-active:contains("NTNU")')->count());
    }

    public function testShowSpecific()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/nyhet/4');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("VEKTOR­PROGRAMMET TILBYR FORELDREKURS!")')->count());

        // Test the sidebar
        $this->assertEquals(1, $crawler->filter('p:contains("VEKAS VEKTOR­ASSISTENT! MØT BEATE!")')->count());

        // Test that the sidebar does not include this article
        $this->assertEquals(0, $crawler->filter('p:contains("VEKTOR­PROGRAMMET TILBYR FORELDREKURS!")')->count());
    }
}
