<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MembersControllerTest extends WebTestCase {
    
	
	public function testIndex() {

        $client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'petjo',
			'PHP_AUTH_PW'   => '1234',
		));

        $crawler = $client->request('GET', '/assistenter');
//
		// Assert a specific 200 status code
		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );

		// Assert that we have the correct page
		$this->assertEquals(1, $crawler->filter('h1:contains("Assistenter")')->count());


		// Assert that we have the correct data
		$this->assertContains( 'Petter', $client->getResponse()->getContent() );
		$this->assertContains( 'Johansen', $client->getResponse()->getContent() );
		$this->assertContains( 'petter@stud.ntnu.no', $client->getResponse()->getContent() );
		$this->assertContains( 'Onsdag', $client->getResponse()->getContent() );
		$this->assertContains( 'Gimse', $client->getResponse()->getContent() );

		// Check the count for the different variables
		$this->assertEquals( 1, $crawler->filter('td:contains("Onsdag")')->count() );
		$this->assertEquals( 1, $crawler->filter('a:contains("Petter Johansen")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("95347865")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("petter@stud.ntnu.no")')->count() );



    }
	
	
}
