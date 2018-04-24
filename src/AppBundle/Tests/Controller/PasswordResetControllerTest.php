<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;

class PasswordResetControllerTest extends BaseWebTestCase
{
    public function testShow()
    {
        $crawler = $this->goTo('/resetpassord');

        $this->assertEquals(1, $crawler->filter('h1:contains("Tilbakestill passord")')->count());
    }
}
