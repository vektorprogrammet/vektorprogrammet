<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ExecutiveBoardControllerTest extends WebTestCase
{
    public function testUpdateExecutiveBoard()
    {
        // ADMIN
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'petjo',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/hovedstyret');

        // Find a link and click it
        $link = $crawler->selectLink('Rediger')->link();
        $crawler = $client->click($link);

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Oppdater Hovedstyret")')->count());

        $form = $crawler->selectButton('Lagre')->form();

        // Change the value of a field
        $form['createExecutiveBoard[name]'] = 'nyttStyre';

        // submit the form
        $crawler = $client->submit($form);

        // Follow the redirect
        $crawler = $client->followRedirect();

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("nyttStyre")')->count());
        $this->assertEquals(0, $crawler->filter('h1:contains("Hovedstyret")')->count());
        \TestDataManager::restoreDatabase();
    }

    public function testAddUserToBoard()
    {
        // ADMIN
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'petjo',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/hovedstyret');

        // Find a link and click it
        $link = $crawler->selectLink('Legg til ny bruker')->eq(0)->link();
        $crawler = $client->click($link);

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Legg til bruker i Hovedstyret")')->count());

        $form = $crawler->selectButton('Lagre')->form();

        // Change the value of a field
        $form['createExecutiveBoardMember[user]']->select(1);
        $form['createExecutiveBoardMember[position]'] = 'Leder';

        // submit the form
        $crawler = $client->submit($form);

        // Follow the redirect
        $crawler = $client->followRedirect();

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Hovedstyret")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Petter")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Johansen")')->count());

        // USER
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'assistent',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/hovedstyret/nytt_medlem/3');

        // Assert that the response is a redirect
        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        // TEAM
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'team',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/hovedstyret/nytt_medlem/3');

        // Assert that the response is a redirect
        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        \TestDataManager::restoreDatabase();
    }

    public function testShowAdmin()
    {
        // ADMIN
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/hovedstyret');

        // Check the count for the different variables
        $this->assertEquals(1, $crawler->filter('h1:contains("Hovedstyret")')->count());

        // TEAM
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'team',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/hovedstyret');

        // Assert that the response is a redirect
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testShow()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/hovedstyret');

        // Assert that we have the correct page
        $this->assertContains('Hovedstyret', $client->getResponse()->getContent());

        // Check the count for the parameters
        $this->assertEquals(1, $crawler->filter('h1:contains("Hovedstyret")')->count());

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
