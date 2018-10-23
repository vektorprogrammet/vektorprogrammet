<?php

namespace Tests\AppBundle\Controller;

use Tests\BaseWebTestCase;

class AboutVektorControllerTest extends BaseWebTestCase
{
    public function testShow()
    {
        $crawler = $this->goTo('/omvektor');

        $this->assertEquals(1, $crawler->filter('h1:contains("Om Vektorprogrammet")')->count());
        $this->assertEquals(1, $crawler->filter('h2:contains("Ofte stilte spørsmål og svar")')->count());
    }
}
