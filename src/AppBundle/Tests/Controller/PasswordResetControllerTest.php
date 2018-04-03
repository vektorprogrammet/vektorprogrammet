<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;

class PasswordResetControllerTest extends BaseWebTestCase
{
    public function testShow()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/resetpassord');

        // Assert that we have the correct amount of data
        $this->assertEquals(1, $crawler->filter('h4:contains("Tilbakestill passord")')->count());

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
