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


    public function testCreate()
    {


        // UNCOMPLETE FUNCTION //
        $client = $this->createAdminClient();
        $crawler = $client->request('GET', '/kontrollpanel/changelog/create');
        $form = $crawler->selectButton('Lagre')->form();
        $date = (new \DateTime())->modify('+1day')->format('d.m.Y H:m');
        $form['change_log[title]'] = 'En ny endring';
        $form['change_log[description]'] = 'Beskrivelse av endringen, slik at du forstår hva som er gjort';
        $form['change_log[gitHubLink]'] = 'https://github.com/vektorprogrammet/vektorprogrammet';
        $form['change_log[date]'] = $date;
        $client->submit($form);
        $crawler = $client->followRedirect();
        $changeLogItemAfter = $crawler->filter('td:contains("En ny endring")')->count();
        $this->assertEquals(1, $changeLogItemAfter);

        // --------------- //
        $client = $this->createAdminClient();
        $crawler = $client->request('GET', '/kontrollpanel/arrangementer/opprett');


    }


}
