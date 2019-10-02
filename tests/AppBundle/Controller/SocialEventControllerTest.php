<?php

namespace Tests\AppBundle\Controller;

use Tests\BaseWebTestCase;

class SocialEventControllerTest extends BaseWebTestCase
{
    public function testShow()
    {
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/arrangementer');
        $this->assertEquals(1, $crawler->filter('h2:contains("Arrangementer")')->count());
    }

    public function testEdit()
    {
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/arrangementer');
        $before = $crawler->filter('td:contains("Endret Arrangement")')->count();
        $this->assertEquals(0,$before);
        $client = $this->createTeamLeaderClient();
        $crawler = $client->click($crawler->selectLink('Endre')->link());
        $form = $crawler->selectButton('Lagre')->form();

        $form['social_event[description]'] = 'Endret Arrangement';
        $client->submit($form);
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/arrangementer');
        $after = $crawler->filter('table.application-table td:contains("Endret Arrangement")')->count();
        $this->assertEquals(1,$after);
    }

    public function testDelete()
    {
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/arrangementer');
        $beforeDelete = $crawler->filter('tr')->count();
        $form = $crawler->selectButton('Slett')->form();
        $client = $this->createTeamLeaderClient();
        $client->submit($form);
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/arrangementer');
        $afterDelete = $crawler->filter('tr')->count();
        $this->assertEquals($beforeDelete,$afterDelete + 1);
    }



}
