<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PasswordResetControllerTest extends WebTestCase {

    public function testShow() {
        $client = static::createClient();

        $crawler = $client->request('GET', '/resetpassord');

        // Assert that we have the correct amount of data
        $this->assertEquals( 1, $crawler->filter('h4:contains("Reset passord")')->count() );

        // Assert a specific 200 status code
        $this->assertEquals( 200, $client->getResponse()->getStatusCode() );

    }

}
