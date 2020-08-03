<?php

namespace Tests\AppBundle\Controller;

use DateTime;
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

        // ensure that event doesn't exist already
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/arrangementer');
        $before = $crawler->filter('td:contains("Nytt TestArrangement")')->count();
        $this->assertEquals(0,$before);

        // create new event
        $client = $this->createTeamLeaderClient();
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/arrangement/opprett');
        $form = $crawler->selectButton('Lagre')->form();

        // add data to event
        $startDate = (new DateTime())->modify('+1day')->format('d.m.Y H:m');
        $endDate = (new DateTime())->modify('+1day + 1hour')->format('d.m.Y H:m');

        $form['social_event[title]'] = 'Nytt TestArrangement';
        $form['social_event[description]'] = 'Beskrivelse av eventet. Skal si noe om hva som skjer.';
        $form['social_event[startTime]'] = $startDate;
        $form['social_event[endTime]'] = $endDate;
        /* NOTE:
               -department,
               -semester
               -role
           are all prefilled
        */

        // save event
        $client->submit($form);

        // Check that event exists
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/arrangementer');
        $after = $crawler->filter('table.application-table td:contains("Beskrivelse av eventet. Skal si noe om hva som skjer.")')->count();
        $this->assertEquals(1,$after);
    }
}
