<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase {
    
	public function testLogin() {

        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $this->assertTrue($crawler->filter('html:contains("Brukernavn")')->count() > 0);
		$this->assertTrue($crawler->filter('html:contains("Passord")')->count() > 0);
		$this->assertTrue($crawler->filter('html:contains("Glemt passord?")')->count() > 0);

		$form = $crawler->selectButton('login')->form();

		// Change the value of a field
		$form['_username'] = 'petjo';
        $form['_password'] = '1234';

		// submit the form
		$client->submit($form);

		$this->assertTrue($client->getResponse()->isRedirect());

		// Follow the redirect
		$crawler = $client->followRedirect();

		// Assert that the response status code is 2xx
		$this->assertTrue($client->getResponse()->isSuccessful());

		$this->assertTrue($crawler->filter('h3:contains("Petter Johansen")')->count() > 0);

    }
	
}
