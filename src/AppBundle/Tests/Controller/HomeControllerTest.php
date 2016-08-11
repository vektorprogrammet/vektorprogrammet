<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase {
	public function testShow() {

        $client = static::createClient();

        $crawler = $client->request('GET', '/');

		// Assert that we have the correct amount of data
		$this->assertEquals( 1, $crawler->filter('h1:contains("KVIFOR MATEMATIKK ER DET VIKTIGASTE DU")')->count() );
		$this->assertEquals( 1, $crawler->filter('h2:contains("Våre sponsorer og partnere")')->count() );
		$this->assertEquals( 1, $crawler->filter('a:contains("Les mer om vektorprogrammet")')->count() );
		$this->assertEquals( 1, $crawler->filter('a:contains("Last ned kompendium")')->count() );

		// Assert a specific 200 status code
		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );

    }
	
}
