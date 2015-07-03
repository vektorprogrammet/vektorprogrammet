<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MembersControllerTest extends WebTestCase {
    
	
	public function testIndex() {
	
        $client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'petjo',
			'PHP_AUTH_PW'   => '1234',
		));
		
        $crawler = $client->request('GET', '/medlemmer');
		
		
		// Assert that we have the correct page 
		$this->assertEquals(1, $crawler->filter('h1:contains("Medlemmer")')->count());
		
	
		// Assert that we have the correct data 
		$this->assertContains( 'Petter', $client->getResponse()->getContent() );
		$this->assertContains( 'Johansen', $client->getResponse()->getContent() );
		$this->assertContains( 'petjo@mail.com', $client->getResponse()->getContent() );
		$this->assertContains( 'Highest admin', $client->getResponse()->getContent() );
		$this->assertContains( 'HiST', $client->getResponse()->getContent() );
		$this->assertContains( 'BITA', $client->getResponse()->getContent() );
		
		// Check the count for the different variables 
		$this->assertEquals( 1, $crawler->filter('a:contains("Petter")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("Johansen")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("petjo@mail.com")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("BITA")')->count() );
		
		// Assert that we have the correct data 
		$this->assertContains( 'Ida', $client->getResponse()->getContent() );
		$this->assertContains( 'Andreassen', $client->getResponse()->getContent() );
		$this->assertContains( 'ida@stud.ntnu.no', $client->getResponse()->getContent() );
		$this->assertContains( 'Admin', $client->getResponse()->getContent() );
		$this->assertContains( 'NTNU2', $client->getResponse()->getContent() );
		$this->assertContains( 'MIDT', $client->getResponse()->getContent() );
		
		// Check the count for the different variables 
		$this->assertEquals( 1, $crawler->filter('a:contains("Ida")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("Andreassen")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("ida@stud.ntnu.no")')->count() );
		$this->assertEquals( 2, $crawler->filter('td:contains("MIDT")')->count() );
		
		// Assert that we have the correct data 
		$this->assertContains( 'Thomas', $client->getResponse()->getContent() );
		$this->assertContains( 'Alm', $client->getResponse()->getContent() );
		$this->assertContains( 'alm@mail.com', $client->getResponse()->getContent() );
		$this->assertContains( 'Superadmin', $client->getResponse()->getContent() );
		$this->assertContains( 'NTNU2', $client->getResponse()->getContent() );
		$this->assertContains( 'MIDT', $client->getResponse()->getContent() );
		
		// Check the count for the different variables 
		$this->assertEquals( 1, $crawler->filter('a:contains("Thomas")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("Alm")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("ida@stud.ntnu.no")')->count() );
		$this->assertEquals( 2, $crawler->filter('td:contains("MIDT")')->count() );

		// Assert a specific 200 status code
		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );
    }
	
	
}
