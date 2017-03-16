<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;

class AboutVektorControllerTest extends BaseWebTestCase
{
    public function testShow()
    {
        $crawler = $this->anonymousGoTo('/omvektor');

        $this->assertEquals(1, $crawler->filter('h1:contains("Om Vektorprogrammet")')->count());
        $this->assertEquals(1, $crawler->filter('h1:contains("Mål og verdier")')->count());
        $this->assertEquals(1, $crawler->filter('h1:contains("Historie")')->count());

        $this->assertEquals(1, $crawler->filter('strong:contains("Vektorprogrammet arbeider for å øke interessen for matematikk og realfag blant elever i grunnskolen. ")')->count());
        $this->assertEquals(1, $crawler->filter('li:contains("Vektorprogrammet ønsker å øke matematikkforståelsen blant elever i grunnskolen.")')->count());
        $this->assertEquals(1, $crawler->filter('p:contains("Vektorprogrammet er et initiativ som ble startet av linjeforeningen Nabla i september 2010")')->count());
    }

    public function testShowFaq()
    {
        $crawler = $this->anonymousGoTo('/faq');

        $this->assertEquals(1, $crawler->filter('h1:contains("Ofte stilte spørsmål")')->count());
    }
}
