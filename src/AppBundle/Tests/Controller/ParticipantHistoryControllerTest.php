<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ParticipantHistoryControllerTest extends WebTestCase {
    
	public function testIndex() {
        $client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'petjo',
			'PHP_AUTH_PW'   => '1234',
		));

        $crawler = $client->request('GET', '/deltakerhistorikk');

		// Assert that we have the correct page
		$this->assertEquals(1, $crawler->filter('h1:contains("Deltakerhistorie")')->count());
		$this->assertEquals(1, $crawler->filter('h3:contains("Arbeidshistorie")')->count());
		$this->assertEquals(1, $crawler->filter('h3:contains("Assistent historie")')->count());

		// Assert that we have the correct data
		$this->assertContains( 'Petter Johansen', $client->getResponse()->getContent() );
		$this->assertContains( 'petter@stud.ntnu.no', $client->getResponse()->getContent() );
		$this->assertContains( 'Hovedstyret', $client->getResponse()->getContent() );
		$this->assertContains( 'NTNU', $client->getResponse()->getContent() );
		$this->assertContains( 'Vår 2016', $client->getResponse()->getContent() );
		$this->assertContains( '-', $client->getResponse()->getContent() );

		// Check the count for the different variables
		$this->assertEquals( 1, $crawler->filter('a:contains("Petter Johansen")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("petter@stud.ntnu.n")')->count() );
		$this->assertEquals( 2, $crawler->filter('td:contains("Hovedstyret")')->count() );
		$this->assertEquals( 3, $crawler->filter('td:contains("NTNU")')->count() );
		$this->assertEquals( 4, $crawler->filter('td:contains("Vår 2016")')->count() );
		$this->assertEquals( 2, $crawler->filter('td:contains("-")')->count() );

		// Assert that we have the correct data
		$this->assertContains( 'Ida Andreassen', $client->getResponse()->getContent() );
		$this->assertContains( 'ida@stud.ntnu.no', $client->getResponse()->getContent() );
		$this->assertContains( 'IT', $client->getResponse()->getContent() );
		$this->assertContains( 'NTNU', $client->getResponse()->getContent() );
		$this->assertContains( 'Vår 2016', $client->getResponse()->getContent() );

		// Check the count for the different variables
		$this->assertEquals( 1, $crawler->filter('a:contains("Ida Andreassen")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("ida@stud.ntnu.no")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("IT")')->count() );

		// Assert a specific 200 status code
		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );
    }
}
