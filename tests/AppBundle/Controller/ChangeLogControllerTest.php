<?php

use Tests\BaseWebTestCase;

class ChangeLogControllerTest extends BaseWebTestCase
{
    public function testCreate()
    {
        $client = $this->createAdminClient();

        $crawler = $client->request('GET', '/kontrollpanel/changelog/create');
        $form = $crawler->selectButton('Lagre')->form();

        $date = (new DateTime())->modify('+1day')->format('d.m.Y H:m');

        $form['change_log[title]'] = 'En ny endring';
        $form['change_log[description]'] = 'Beskrivelse av endringen, slik at du forstår hva som er gjort';
        $form['change_log[gitHubLink]'] = 'https://github.com/vektorprogrammet/vektorprogrammet';
        $form['change_log[date]'] = $date;

        $client->submit($form);

        $crawler = $client->followRedirect();
        $changeLogItemAfter = $crawler->filter('td:contains("En ny endring")')->count();
        $this->assertEquals(1, $changeLogItemAfter);

    }

    public function testEdit()
    {
        $crawler = $this->teamMemberGoTo('/kontrollpanel/changelog/edit/1');
        $before = $crawler->filter('td:contains("Endret changelog-objektet")')->count();
        $this->assertEquals(0, $before);

        $client = $this->createTeamMemberClient();
        $crawler = $client->request('GET', '/kontrollpanel/changelog/edit/1');
        $form = $crawler->selectButton('Lagre')->form();

        $date = (new DateTime())->modify('+1day')->format('d.m.Y H:m');

        $form['change_log[title]'] = 'Endrer changelog-objektet';
        $form['change_log[description]'] = 'Beskrivelse av den rette endringen, slik at du nå forstå hva som er gjort';
        $form['change_log[gitHubLink]'] = 'https://github.com/vektorprogrammet/vektorprogrammet';
        $form['change_log[date]'] = $date;

        $client->submit($form);

        $crawler = $client->followRedirect();
        $changeLogItemAfter = $crawler->filter('td:contains("Endrer changelog-objektet")')->count();
        $this->assertEquals(1, $changeLogItemAfter);
    }

    public function testDelete()
    {
        $client = $this->createTeamMemberClient();
        $client->request('POST', '/kontrollpanel/changelog/delete/1');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->request('POST', '/kontrollpanel/changelog/delete/1');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testShow()
    {
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/changelog/show/all');
        $table_check = $crawler->filter('.card-header:contains("Changelog-objekter")');
        $this->assertEquals(1, $table_check->count());
    }


}
