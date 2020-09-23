<?php

namespace Tests\AppBundle\Controller;

use Tests\BaseWebTestCase;
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

    public function testAddOneTeamMember()
    {
        $client = self::createAdminClient();

        $lengthTeamOld = $this->generateListCountChars($client, 'Team');

        // Get a user email and add user to a team
        $userID = 23;

        $crawler = $this->goTo('/kontrollpanel/teamadmin/team/nytt_medlem/2', $client);
        $form = $crawler->selectButton('Legg til')->form();
        $form['createTeamMembership[user]'] = $userID;
        $form['createTeamMembership[position]'] = 2;
        $form['createTeamMembership[startSemester]'] = 2;
        $client->submit($form);
        $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());

        $user = $this->em->getRepository('AppBundle:User')->find($userID);
        $this->assertNotNull($user);
        $userEmailLength = strlen($user->getCompanyEmail());

        $lengthTeamNew = $this->generateListCountChars($client, 'Team');

        // Add 2 for comma and whitespace
        $this->assertEquals($lengthTeamOld + $userEmailLength + 2, $lengthTeamNew);
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

    public function testTeamAddAssistantIsAll()
    {
        $client = self::createAdminClient();

        $lengthAssistants = $this->generateListCountChars($client, 'Assistent');
        $lengthTeam = $this->generateListCountChars($client, 'Team');
        $lengthAll = $this->generateListCountChars($client, 'Alle');

        $this->assertGreaterThan(0, $lengthAssistants);
        $this->assertGreaterThan(0, $lengthTeam);
        $this->assertGreaterThan($lengthAssistants, $lengthAll);
        $this->assertGreaterThan($lengthTeam, $lengthAll);
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
        $form['generate_mailing_list[semester]'] = 1;
        $form['generate_mailing_list[type]'] = $type;
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());

        return strlen($crawler->filter('pre')->text());
    }
}
