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
        $client = $this->createAnonymousClient();

        $course = $this->em->getRepository('AppBundle:ParentCourse')->findOneBy(array('speaker'=> $this->speaker));
        $id = $course->getId();

        $crawler = $client->request('GET', '/foreldrekurs/pamelding/'.$id);

        $form = $crawler->selectButton('Lagre')->form();

        $form['parent_assignment[name]'] = 'Ola Nordmann';
        $form['parent_assignment[email]'] = 'ola@nordmann.no';

        $client->submit($form);

        $adminCrawler = $this->teamLeaderGoTo('/kontrollpanel/foreldrekurs/foreldre/'.$id);
        $table_check = $adminCrawler->filter('.card-header:contains("Påmeldte foreldre hos ")')->count();
        $this->assertEquals(1, $table_check);
    }


    public function testShow()
    {
        $course = $this->em->getRepository('AppBundle:ParentCourse')->findOneBy(array('speaker'=> $this->speaker));
        $id = $course->getId();
        $crawler = $this->anonymousGoTo('/foreldre/foreldrekurs/'.$id);
        $course_check = $crawler->selectLink("Meld meg på nå")->count();
        $this->assertEquals(1, $course_check);
    }



    /*
    # This test should wait until the controller-action for this is actually finished!
    public function testExternalDelete()
    {
        $client = $this->createTeamMemberClient();
        $course = $this->em->getRepository('AppBundle:ParentCourse')->findOneBy(array('speaker'=> $this->speaker));
        $id = $course->getId();
        $client->request('POST', '/kontrollpanel/foreldrekurs/foreldre/slett/'.$id);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->request('POST', '/kontrollpanel/foreldrekurs/foreldre/slett/'.$id);f
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
    */


    public function testSendEmail()
    {
        $client = $this->createAnonymousClient();
        #$this->$speaker = 'Eivind Kopperud';
        $course = $this->em->getRepository('AppBundle:ParentCourse')->findOneBy(array('speaker'=> $this->speaker));

        $id = $course->getId();

        $crawler = $this->anonymousGoTo('/foreldrekurs/pamelding/'.$id);
        $form = $crawler->selectButton('parent_assignment[save]')->form();
        $form['parent_assignment[name]'] = "Alex";
        $form['parent_assignment[email]'] = 'alexaoh@stud.ntnu.no';

        $client->enableProfiler();
        $client->submit($form);


        // Assert email sent
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $mailCollector = $client->getProfile()->getCollector('swiftmailer');
        $this->assertEquals(1, $mailCollector->getMessageCount());

        // Assert that the body is correct.
        $emailstring = 'Du er nå påmeldt til foreldrekurs';
        $message = $mailCollector->getMessages()[0];
        $body = $message->getBody();
        $findString = (strpos($body, $emailstring) !== false ? true : false);
        $this->assertEquals($findString, true);
    }

}
