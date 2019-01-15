<?php

namespace Tests\AppBundle\Controller;

use Tests\BaseWebTestCase;

class TodoListControllerTest extends BaseWebTestCase
{
    public function testShow()
    {
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/sjekkliste');
        $this->assertEquals(1, $crawler->filter('h2:contains("Sjekkliste")')->count());
    }

    public function testToggle()
    {
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/sjekkliste');
        $beforeToggle = $crawler->filter('.fa-check-square')->count();
        $client = $this->createTeamLeaderClient();
        $form = $crawler->filter('.btn.btn-link')->form();
        $client->submit($form);
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/sjekkliste');
        $afterToggle = $crawler->filter('.fa-check-square')->count();
        $this->assertNotEquals($beforeToggle, $afterToggle);
    }

    public function testDelete()
    {
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/sjekkliste');
        $beforeDelete = $crawler->filter('tr')->count();
        $form = $crawler->selectButton('Slett')->form();
        $client = $this->createTeamLeaderClient();
        $client->submit($form);
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/sjekkliste');
        $afterDelete = $crawler->filter('tr')->count();
        $this->assertEquals($beforeDelete,$afterDelete + 1);
    }

    public function testEdit()
    {
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/sjekkliste');
        $before = $crawler->filter('td:contains("Endret Gjøremål")')->count();
        $this->assertEquals(0,$before);
        $client = $this->createTeamLeaderClient();
        $crawler = $client->click($crawler->selectLink('Endre')->link());
        $form = $crawler->selectButton('Endre')->form();

        $form['create_todo_item_info[description]'] = 'Endret Gjøremål';
        $client->submit($form);
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/sjekkliste');
        $after = $crawler->filter('table.application-table td:contains("Endret Gjøremål")')->count();
        $this->assertEquals(1,$after);
    }

}
