<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;

class AboutVektorControllerTest extends BaseWebTestCase
{
    public function testShow()
    {
        $crawler = $this->goTo('/omvektor');

        $this->assertEquals(1, $crawler->filter('h1:contains("Om Vektorprogrammet")')->count());
        $this->assertEquals(1, $crawler->filter('h2:contains("Ofte stilte spørsmål og svar")')->count());
    }
}
