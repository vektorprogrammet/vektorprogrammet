<?php

use Tests\BaseWebTestCase;

class ChangeLogControllerTest extends BaseWebTestCase
{
    public function testCreate()
    {
        $client = $this->createAdminClient();

        $crawler = $client->request('GET', '/kontrollpanel/changelog/create');
        $form = $crawler->selectButton('Lagre')->form();

        $date = (new \DateTime())->modify('+1day')->format('d.m.Y H:m');

        $form['change_log[title]'] = 'En ny endring';
        $form['change_log[description]'] = 'Beskrivelse av endringen, slik at du forstår hva som er gjort';
        $form['change_log[gitHubLink]'] = 'https://github.com/vektorprogrammet/vektorprogrammet';
        $form['change_log[date]'] = $date;

        $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $crawler = $client->followRedirect();
        $changeLogItemAfter = $crawler->filter('td:contains("En ny endring")')->count();
        $this->assertEquals(1, $changeLogItemAfter);

    }
}
