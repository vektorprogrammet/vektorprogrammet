<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;

class ImageGalleryControllerTest extends BaseWebTestCase
{
    private function fillForm($form)
    {
        $form['imageGallery[title]'] = 'En bra tittel';
        $form['imageGallery[referenceName]'] = 'Et informativt referansenavn';
        $form['imageGallery[description]'] = 'En flott beskrivelse';
        return $form;
    }

    public function testCreate()
    {
        $client = $this->createTeamLeaderClient();
        $crawler = $client->request('GET', '/kontrollpanel/bildegallerier');
        $form = $crawler->selectButton('Opprett bildegalleri')->form();

        $client->submit($this->fillForm($form));
        $this->assertEquals(1, $client->followRedirect()->filter('h1:contains("En bra tittel")')->count());
    }

    public function testEdit()
    {
        $client = $this->createTeamLeaderClient();
        $crawler = $client->request('GET', '/kontrollpanel/bildegallerier/galleri/1');
        $form = $crawler->selectButton('Lagre endringer')->form();

        $client->submit($this->fillForm($form));
        $crawler = $client->followRedirect();
        $this->assertEquals(1, $crawler->filter('h1:contains("En bra tittel")')->count());
        $this->assertEquals(1, $crawler->filter('p:contains("En flott beskrivelse")')->count());
    }

    public function testDelete()
    {
        // Create an empty gallery
        $client = $this->createTeamLeaderClient();
        $crawler = $client->request('GET', '/kontrollpanel/bildegallerier');
        $form = $crawler->selectButton('Opprett bildegalleri')->form();

        $client->submit($this->fillForm($form));

        // Delete this
        $crawler = $client->followRedirect();
        $deleteButton = $crawler->filter('.secretly-not-a-button')->first()->form();
        $client->submit($deleteButton);
        $crawler = $client->followRedirect();
        $this->assertEquals(0, $crawler->filter('a:contains("En bra tittel")')->count());
    }
}
