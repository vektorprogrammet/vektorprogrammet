<?php

use Tests\BaseWebTestCase;

class ParentAssignmentAdminControllerTest extends BaseWebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    private $speaker = "Eivind Kopperud";

    public function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }
    public function testCreate()
    {
        $client = $this->createAdminClient();

        $course = $this->em->getRepository('AppBundle:ParentCourse')->findOneBy(array('speaker'=> $this->speaker));
        $id = $course->getId();

        $crawler = $client->request('GET', '/kontrollpanel/foreldrekurs/pamelding/'.$id);

        $form = $crawler->selectButton('Lagre')->form();

        $form['parent_assignment[name]'] = 'Ola Nordmann';
        $form['parent_assignment[email]'] = 'ola@nordmann.no';

        $client->submit($form);

        $crawler = $client->followRedirect();
        $parentAssignmentAdminAfter = $crawler->filterXPath("//td[contains(text(), 'Ola Nordmann')]")->count();
        #dump($parentAssignmentAdminAfter);
        $this->assertEquals(1,$parentAssignmentAdminAfter);

    }

    public function testShow()
    {
        $course = $this->em->getRepository('AppBundle:ParentCourse')->findOneBy(array('speaker'=> $this->speaker));
        $id = $course->getId();
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/foreldrekurs/foreldre/'.$id);
        $table_check = $crawler->filter('.card-header:contains("Påmeldte foreldre hos ")')->count();
        $this->assertEquals(1, $table_check);
    }

    public function testDelete()
    {
        $client = $this->createTeamMemberClient();
        $course = $this->em->getRepository('AppBundle:ParentCourse')->findOneBy(array('speaker'=> $this->speaker));
        $id = $course->getId();
        $client->request('POST', '/kontrollpanel/foreldrekurs/foreldre/slett/'.$id);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->request('POST', '/kontrollpanel/foreldrekurs/foreldre/slett/'.$id);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

}
