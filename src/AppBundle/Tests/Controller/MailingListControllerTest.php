<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class MailingListControllerTest extends BaseWebTestCase
{
    public function testTeamAddAssistantIsAll()
    {
        $client = self::createAdminClient();

        $length_assistants = $this->generateListCountChars($client, 'Assistent');
        $length_team = $this->generateListCountChars($client, 'Team');
        $length_all = $this->generateListCountChars($client, 'Alle');

        $this->assertEquals($length_assistants + $length_team, $length_all);
    }

    /**
     * @param Client $client
     * @param string $type
     *
     * @return int
     */
    private function generateListCountChars(Client $client, string $type)
    {
        $crawler = $this->goTo('/kontrollpanel/epostlister', $client);
        $form = $crawler->selectButton('Generer')->form();
        $form['generate_mailing_list[semester]'] = 2;
        $form['generate_mailing_list[type]'] = $type;
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());

        return strlen($crawler->filter('pre')->text());
    }
}
