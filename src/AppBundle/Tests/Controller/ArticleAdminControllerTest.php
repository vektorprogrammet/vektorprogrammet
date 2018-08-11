<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;

class ArticleAdminControllerTest extends BaseWebTestCase
{
    public function testShow()
    {
        // Team user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'team',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/artikkeladmin');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Artikkel")')->count());

        // Assert that we have the correct buttons
        $this->assertEquals(1, $crawler->filter('a:contains("Ny Artikkel")')->count());
        $this->assertGreaterThan(0, $crawler->filter('a:contains("Sticky")')->count());
        $this->assertGreaterThan(0, $crawler->filter('a:contains("Rediger")')->count());
        $this->assertGreaterThan(0, $crawler->filter('a:contains("Slett")')->count());

        // User
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'assistent',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/artikkeladmin');

        // Assert that the page response status code is 403 Access denied
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    /*
    Requires JQuery interaction, Symfony2 does not support that

    Phpunit was designed to test the PHP language, have to use another tool to test these.

    public function testSticky() {}
    public function testDelete() {}

    */
}
