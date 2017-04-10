<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class MailingListControllerTest extends BaseWebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    public function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testAddOneAssistant()
    {
        $client = self::createAdminClient();

        $length_team_old = $this->generateListCountChars($client, 'Team');

        // Get a user email and add user to a team
        $user_id = 22;
        $user = $this->em->getRepository('AppBundle:User')->find($user_id);
        $this->assertNotNull($user);
        $user_email_length = strlen($user->getEmail());

        $crawler = $this->goTo('/kontrollpanel/teamadmin/team/nytt_medlem/2', $client);
        $form = $crawler->selectButton('Opprett')->form();
        $form['createWorkHistory[user]'] = $user_id;
        $form['createWorkHistory[position]'] = 2;
        $form['createWorkHistory[startSemester]'] = 2;
        $client->submit($form);
        $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());

        $length_team_new = $this->generateListCountChars($client, 'Team');

        // Add 2 for comma and whitespace
        $this->assertEquals($length_team_old + $user_email_length + 2, $length_team_new);

        \TestDataManager::restoreDatabase();
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

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
