<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SubstituteControllerTest extends WebTestCase
{

    public function testShow()
    {
        // Admin user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/vikar');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Vikarer")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Marius")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Svendsen")')->count());

        // Assert that we have the edit/delete buttons as admin (1 delete button is the hidden modal button, so we have to check > 1)
        $this->assertGreaterThan(1, $crawler->filter('a:contains("Slett")')->count());
        $this->assertGreaterThan(0, $crawler->filter('a:contains("Rediger")')->count());


        // Team user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'team',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/vikar');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Vikarer")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Marius")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Svendsen")')->count());

        // Assert that we don't have the edit/delete buttons as team (1 delete button is the hidden modal button, so we have to check = 1)
        $this->assertEquals(1, $crawler->filter('a:contains("Slett")')->count());
        $this->assertEquals(0, $crawler->filter('a:contains("Rediger")')->count());


        // Assistant user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'assistent',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/vikar');

        // Assert that the page response status code is 403 access denied
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testShowSubstitutesByDepartment()
    {
        // Admin user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/vikar/1');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Vikarer")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Marius")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Svendsen")')->count());

        // Assert that we have the edit/delete buttons as admin (1 delete button is the hidden modal button, so we have to check > 1)
        $this->assertGreaterThan(1, $crawler->filter('a:contains("Slett")')->count());
        $this->assertGreaterThan(0, $crawler->filter('a:contains("Rediger")')->count());


        // Team user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'team',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/vikar/1');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Vikarer")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Marius")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Svendsen")')->count());

        // Assert that we don't have the edit/delete buttons as team (1 delete button is the hidden modal button, so we have to check = 1)
        $this->assertEquals(1, $crawler->filter('a:contains("Slett")')->count());
        $this->assertEquals(0, $crawler->filter('a:contains("Rediger")')->count());


        // Assistant user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'assistent',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/vikar/1');

        // Assert that the page response status code is 403 access denied
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testEdit()
    {
        // Admin user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/vikar/rediger/1');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Endre vikarinformasjon")')->count());

        // Find the form
        $form = $crawler->selectButton('Lagre')->form();

        // Fill in the form
        $form['substitute[phone]'] = "95999999";

        // Submit the form
        $client->submit($form);

        // Follow the redirect
        $crawler = $client->followRedirect();

        // Assert that we have the correct page with the correct info (from the submitted form)
        $this->assertEquals(1, $crawler->filter('h1:contains("Vikarer")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("95999999")')->count());


        // Team user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'team',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/vikar/rediger/1');

        // Assert that the page response status code is 403 access denied
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    /*
    Requires JQuery interaction, Symfony2 does not support that

    Phpunit was designed to test the PHP language, have to use another tool to test these.

    public function testDelete() {}
    public function testCreate() {}
    */

}