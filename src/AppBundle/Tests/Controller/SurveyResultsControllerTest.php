<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SurveyResultsControllerTest extends WebTestCase
{

    public function testShowPupil()
    {

        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => '1234',
        ));

        $crawler = $client->request('GET', '/statistikk/undersokelse/elev');

        // Assert that we have the correct department data
        //ÆØÅ does not work with PHPUnit
        $this->assertContains('Antall svar per skole', $client->getResponse()->getContent());
        //$this->assertContains('Kjønnsfordeling', $client->getResponse()->getContent());
        $this->assertContains('Klassefordeling', $client->getResponse()->getContent());
        $this->assertContains('Var tilstede', $client->getResponse()->getContent());
        //$this->assertContains('Har fått hjelp av vektorassistentene', $client->getResponse()->getContent());
        //$this->assertContains('Det gikk greit å spørre om hjelp', $client->getResponse()->getContent());
        //$this->assertContains('Lettere å spørre om hjelp studentene i timen', $client->getResponse()->getContent());
        $this->assertContains('Studentene kunne pensum', $client->getResponse()->getContent());
        //$this->assertContains('Mer spennende å jobbe med matte', $client->getResponse()->getContent());
        $this->assertContains('Jeg vil at assistentene kommer tilbake', $client->getResponse()->getContent());

    }


    public function testShowTeacher()
    {

        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => '1234',
        ));

        $crawler = $client->request('GET', '/statistikk/undersokelse/laerer');

        // Assert that we have the correct department data
        //ÆØÅ does not work with PHPUnit
        $this->assertContains('Antall svar per skole', $client->getResponse()->getContent());
        $this->assertContains('Klassetrinn', $client->getResponse()->getContent());
        //$this->assertContains('Det var nyttig å ha vektorassistentene i klassen.', $client->getResponse()->getContent());
        $this->assertContains('Vektorassistentene var kvalifiserte for jobben.', $client->getResponse()->getContent());
        $this->assertContains('Det var for mange studentassistenter tilstede.', $client->getResponse()->getContent());
        //$this->assertContains('Det var god kontakt og informasjonsflyt på forhånd.', $client->getResponse()->getContent());
        //$this->assertContains('Jeg ønsker at prosjektet fortsetter.', $client->getResponse()->getContent());
        //$this->assertContains('Jeg tror elevene har blitt mer motivert for matematikk som følge av prosjektet.', $client->getResponse()->getContent());
        //$this->assertContains('Arbeidsbelastningen ble mindre når vektorassistentene var på skolen.', $client->getResponse()->getContent());
        $this->assertContains('Undervisning ble bedre tilpasset for elevene.', $client->getResponse()->getContent());
        //$this->assertContains('Har du noen kommentarer om vektorprogrammet som vi kan bruke videre?', $client->getResponse()->getContent());


    }

}