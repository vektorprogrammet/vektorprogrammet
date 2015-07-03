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
		$this->assertEquals(1, $crawler->filter('h1:contains("Deltaker historikk")')->count());
		$this->assertEquals(1, $crawler->filter('h3:contains("Arbeidshistorie")')->count());
		$this->assertEquals(1, $crawler->filter('h3:contains("Assistent historie")')->count());
	
		// Assert that we have the correct data 
		$this->assertContains( 'Petter Johansen', $client->getResponse()->getContent() );
		$this->assertContains( 'petjo@mail.com', $client->getResponse()->getContent() );
		$this->assertContains( 'Hovedstyret', $client->getResponse()->getContent() );
		$this->assertContains( 'NTNU2', $client->getResponse()->getContent() );
		$this->assertContains( '01/04/14', $client->getResponse()->getContent() );
		$this->assertContains( '30/05/15', $client->getResponse()->getContent() );
		
		// Check the count for the different variables 
		$this->assertEquals( 1, $crawler->filter('a:contains("Petter Johansen")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("petjo@mail.com")')->count() );
		$this->assertEquals( 2, $crawler->filter('td:contains("Hovedstyret")')->count() );
		$this->assertEquals( 2, $crawler->filter('td:contains("NTNU2")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("01/04/14")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("30/05/15")')->count() );
		
		// Assert that we have the correct data 
		$this->assertContains( 'Aleksander Tryggan', $client->getResponse()->getContent() );
		$this->assertContains( 'u@b.c', $client->getResponse()->getContent() );
		$this->assertContains( 'Hovedstyret', $client->getResponse()->getContent() );
		$this->assertContains( 'NTNU2', $client->getResponse()->getContent() );
		$this->assertContains( '03/10/90', $client->getResponse()->getContent() );
		$this->assertContains( '03/10/16', $client->getResponse()->getContent() );
		
		// Check the count for the different variables 
		$this->assertEquals( 1, $crawler->filter('a:contains("Aleksander Tryggan")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("u@b.c")')->count() );
		$this->assertEquals( 2, $crawler->filter('td:contains("Hovedstyret")')->count() );
		$this->assertEquals( 2, $crawler->filter('td:contains("NTNU2")')->count() );
		$this->assertEquals( 2, $crawler->filter('td:contains("03/10/90")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("03/10/16")')->count() );
		
		
		// Assert that we have the correct data 
		$this->assertContains( 'Markus Gundersen', $client->getResponse()->getContent() );
		$this->assertContains( 'aah@b.c', $client->getResponse()->getContent() );
		$this->assertContains( 'Pølseteamet', $client->getResponse()->getContent() );
		$this->assertContains( 'HiST', $client->getResponse()->getContent() );
		$this->assertContains( '03/10/90', $client->getResponse()->getContent() );
		$this->assertContains( '03/10/10', $client->getResponse()->getContent() );
		
		// Check the count for the different variables 
		$this->assertEquals( 1, $crawler->filter('a:contains("Markus Gundersen")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("aah@b.c")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("Pølseteamet")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("HiST")')->count() );
		$this->assertEquals( 2, $crawler->filter('td:contains("03/10/90")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("03/10/10")')->count() );
		
		
		// Assert a specific 200 status code
		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );
    }
}
