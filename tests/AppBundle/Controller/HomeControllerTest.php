<?php

namespace Tests\AppBundle\Controller;

use Tests\BaseWebTestCase;

class HomeControllerTest extends BaseWebTestCase
{
    public function testShow()
    {
        $crawler = $this->goTo('/');

        $this->assertEquals(1, $crawler->filter('h4:contains("KVIFOR MATEMATIKK ER DET VIKTIGASTE DU")')->count());
        $this->assertEquals(1, $crawler->filter('h2:contains("Hovedsponsorer")')->count());
    }
}
