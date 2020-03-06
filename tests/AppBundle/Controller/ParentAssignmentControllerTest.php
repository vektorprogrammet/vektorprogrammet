<?php

use Tests\BaseWebTestCase;

class ParentAssignmentAdminControllerTest extends BaseWebTestCase
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
    /*
    public function testCreate()
    {
        $client = $this->createAnonymousClient();

        $speaker = 'CourseToBeAssignedTo';
        $course = $this->em->getRepository('AppBundle:ParentCourse')->findOneBy(array('speaker'=> $speaker));
        $id = $course->getId();

        $crawler = $client->request('GET', '/foreldrekurs/pamelding/'.$id);

        $form = $crawler->selectButton('Lagre')->form();

        $form['parent_assignment[navn]'] = 'Ola Nordmann';
        $form['parent_assignment[epost]'] = 'ola@nordmann.no';

        $client->submit($form);

        $adminClient = $this->createAdminClient();

        $adminCrawler = $this->teamLeaderGoTo('/kontrollpanel/foreldrekurs/foreldre/'.$id);
        $table_check = $adminCrawler->filter('.card-header:contains("Påmeldte foreldre hos ")')->count();
        $this->assertEquals(1, $table_check);
    }

    */
    public function testShow()
    {
        $speaker = 'CourseToBeAssignedTo';
        $course = $this->em->getRepository('AppBundle:ParentCourse')->findOneBy(array('speaker'=> $speaker));
        $id = $course->getId();
        $crawler = $this->anonymousGoTo('/foreldre/foreldrekurs/'.$id);
        $course_check = $crawler->filterXPath("//*[contains(@custom_html, 'CourseToBeAssignedTo')]")->count();
        #dump($course_check);
        $this->assertEquals(1, $course_check);
    }

    /*

    public function testExternalDelete()
    {
        $client = $this->createTeamMemberClient();
        $speaker = 'CourseToBeAssignedTo';
        $course = $this->em->getRepository('AppBundle:ParentCourse')->findOneBy(array('speaker'=> $speaker));
        $id = $course->getId();
        $client->request('POST', '/kontrollpanel/foreldrekurs/foreldre/slett/'.$id);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->request('POST', '/kontrollpanel/foreldrekurs/foreldre/slett/'.$id);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
    */

}
