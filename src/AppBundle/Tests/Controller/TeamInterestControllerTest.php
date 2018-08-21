<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;

class TeamInterestControllerTest extends BaseWebTestCase
{
    public function testShowTeamInterestForm()
    {
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/opptakadmin/teaminteresse/2');
        $rowsBefore = $crawler->filter('tr')->count();

        $client = $this->createAnonymousClient();
        $crawler = $client->request('GET', '/teaminteresse/1');
        $form = $crawler->selectButton('Send')->form();
        $form['appbundle_teaminterest[name]'] = 'Test Testesen';
        $form['appbundle_teaminterest[email]'] = 'test@testmail.com';
        $form['appbundle_teaminterest[potentialTeams]'][3]->tick();
        $this->createAnonymousClient()->submit($form);
        $this->assertEquals(302, $client->getResponse()->getStatusCode()); // Assert request was redirected

        $crawler = $this->teamLeaderGoTo('/kontrollpanel/opptakadmin/teaminteresse/2');
        $rowsAfter = $crawler->filter('tr')->count();
        $this->assertEquals($rowsBefore + 2, $rowsAfter);
    }
}
