<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SurveyControllerTest extends WebTestCase
{

    public function testShowPupil()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => '1234',
        ));

        $crawler = $client->request('GET', '/undersokelse/elev');

        // Assert that we have the correct department data
        $this->assertContains('Skole', $client->getResponse()->getContent());
        $this->assertContains('1. Kjønn', $client->getResponse()->getContent());
        $this->assertContains('2. Klassetrinn', $client->getResponse()->getContent());
        $this->assertContains('3. Jeg var tilstede da vektorassistentene kom på besøk for å hjelpe', $client->getResponse()->getContent());
        $this->assertContains('4. Jeg har fått hjelp av vektorassistentene', $client->getResponse()->getContent());
        $this->assertContains('5. Jeg syntes det gikk greit å spørre vektorassistentene om hjelp', $client->getResponse()->getContent());
        $this->assertContains('6. Jeg fikk lettere hjelp da vektorassistentene var i timen', $client->getResponse()->getContent());
        $this->assertContains('7. Vektorassistentene kunne pensum og forklarte slik at jeg fikk mer forståelse for oppgaven', $client->getResponse()->getContent());
        $this->assertContains('8. Jeg synes matte er mer spennende etter møtet med vektorassistentene', $client->getResponse()->getContent());
        $this->assertContains('9. Jeg vil at vektorassistentene kommer tilbake', $client->getResponse()->getContent());


        // Assert a specific 200 status code
        $this->assertEquals( 200, $client->getResponse()->getStatusCode() );

    }

    public function testShowTeacher()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => '1234',
        ));

        $crawler = $client->request('GET', '/undersokelse/laerer');

        // Assert that we have the correct department data
        $this->assertContains('Skole', $client->getResponse()->getContent());
        $this->assertContains('1. Klassetrinn', $client->getResponse()->getContent());
        $this->assertContains('2. Det var nyttig å ha vektorassistentene i klassen.', $client->getResponse()->getContent());
        $this->assertContains('3. Vektorassistentene var kvalifiserte for jobben.', $client->getResponse()->getContent());
        $this->assertContains('4. Det var for mange studentassistenter tilstede.', $client->getResponse()->getContent());
        $this->assertContains('5. Det var god kontakt og informasjonsflyt på forhånd.', $client->getResponse()->getContent());
        $this->assertContains('6. Jeg ønsker at prosjektet fortsetter.', $client->getResponse()->getContent());
        $this->assertContains('7. Jeg tror elevene har blitt mer motivert for matematikk som følge av prosjektet.', $client->getResponse()->getContent());
        $this->assertContains('8. Arbeidsbelastningen ble mindre når vektorassistentene var på skolen.', $client->getResponse()->getContent());
        $this->assertContains('9. Undervisning ble bedre tilpasset for elevene.', $client->getResponse()->getContent());
        $this->assertContains('10. Har du noen kommentarer om vektorprogrammet som vi kan bruke videre?', $client->getResponse()->getContent());

        // Assert a specific 200 status code
        $this->assertEquals( 200, $client->getResponse()->getStatusCode() );

    }
}