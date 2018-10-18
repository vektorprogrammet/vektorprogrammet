<?php

namespace Tests\AppBundle\Controller;

use Tests\BaseWebTestCase;

class ArticleAdminControllerTest extends BaseWebTestCase
{
    public function testTeamMemberShow()
    {
        // Team user
        $client = $this->createTeamMemberClient();

        $crawler = $client->request('GET', '/kontrollpanel/artikkeladmin');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct buttons
        $this->assertEquals(1, $crawler->filter('a:contains("Ny Artikkel")')->count());
        $this->assertGreaterThan(0, $crawler->filter('a:contains("Rediger")')->count());
        $this->assertEquals(0, $crawler->filter('button:contains("Slett")')->count());
    }

    public function testTeamLeaderShow()
    {
        // Team user
        $client = $this->createTeamLeaderClient();

        $crawler = $client->request('GET', '/kontrollpanel/artikkeladmin');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct buttons
        $this->assertEquals(1, $crawler->filter('a:contains("Ny Artikkel")')->count());
        $this->assertGreaterThanOrEqual(1, $crawler->filter('a:contains("Rediger")')->count());
        $this->assertGreaterThanOrEqual(1, $crawler->filter('button:contains("Slett")')->count());
    }
}
