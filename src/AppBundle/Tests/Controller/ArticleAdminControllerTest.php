<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ArticleAdminControllerTest extends WebTestCase
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

    public function testCreate()
    {
        // Team user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'team',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/artikkeladmin/opprett');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Legg til en ny artikkel")')->count());

        // Fill in the form
        $form = $crawler->selectButton('Publiser')->form();
        $form['article[title]'] = 'test tittel';
        $form['article[article]'] = 'test artikkel';
        $form['article[imageLarge]'] = 'noImage';
        $form['article[imageMedium]'] = 'noImage';
        $form['article[imageSmall]'] = 'noImage';

        // submit the form
        $crawler = $client->submit($form);

        // Follow the redirect
        $crawler = $client->followRedirect();

        // Assert that we have the correct page with the correct values
        $this->assertContains('Artikkelen har blitt publisert.', $client->getResponse()->getContent());
        $this->assertEquals(1, $crawler->filter('h1:contains("test tittel")')->count());
        $this->assertContains('test artikkel', $client->getResponse()->getContent());

        // User
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'assistent',
            'PHP_AUTH_PW' => '1234',
        ));

        $client->request('GET', '/kontrollpanel/artikkeladmin/opprett');

        // Assert that the page response status code is 403 Access denied
        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        \TestDataManager::restoreDatabase();
    }

    public function testEdit()
    {
        // Team user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'team',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/artikkeladmin/rediger/1');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Endre artikkel")')->count());

        // Fill in the form
        $form = $crawler->selectButton('Publiser')->form();
        $form['article[title]'] = 'Ny test tittel';

        // submit the form
        $crawler = $client->submit($form);

        // Follow the redirect
        $crawler = $client->followRedirect();

        // Assert that we have the correct page with the new value
        $this->assertContains('Endringene har blitt publisert.', $client->getResponse()->getContent());
        $this->assertEquals(1, $crawler->filter('h1:contains("Ny test tittel")')->count());

        // User
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'assistent',
            'PHP_AUTH_PW' => '1234',
        ));

        $client->request('GET', '/kontrollpanel/artikkeladmin/rediger/1');

        // Assert that the page response status code is 403 Access denied
        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        \TestDataManager::restoreDatabase();
    }

    /*
    Requires JQuery interaction, Symfony2 does not support that

    Phpunit was designed to test the PHP language, have to use another tool to test these.

    public function testSticky() {}
    public function testDelete() {}

    */
}
