<?php

use Tests\BaseWebTestCase;

class ParentCourseControllerTest extends BaseWebTestCase
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


    public function testCreate()
    {
        $client = $this->createAdminClient();

        $crawler = $client->request('GET', '/kontrollpanel/foreldrekurs/create');
        $form = $crawler->selectButton('Meld på!')->form();

        $date = (new \DateTime())->modify('+1day')->format('d.m.Y H:m');

        $form['parent_course[speaker]'] = 'Ola Nordmann';
        $form['parent_course[place]'] = 'Auditorium R5, NTNU Gløshaugen';
        $form['parent_course[date]'] = $date;
        $form['parent_course[information]'] = 'Dette kurset skal handle om hvordan DU som forelder kan bidra på best mulig måte i ditt barns læring!';

        $client->submit($form);

        $crawler = $client->followRedirect();

        #dump($crawler);

        $parentCourseItemAfter = $crawler->filterXPath("//td[contains(text(), 'Ola Nordmann')]")->count();
        $this->assertEquals(1, $parentCourseItemAfter);

    }


    public function testShow()
    {
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/foreldrekurs');
        $table_check = $crawler->filter('.card-header:contains("Antall planlagte foreldrekurs")');
        $this->assertEquals(1, $table_check->count());
    }

    public function testDelete()
    {
        $client = $this->createTeamMemberClient();

        $client = $this->createTeamMemberClient();
        $speaker = 'CourseToBeAssignedTo';
        $course = $this->em->getRepository('AppBundle:ParentCourse')->findOneBy(array('speaker'=> $speaker));
        $id = $course->getId();

        $client->request('POST', '/kontrollpanel/foreldrekurs/slett/'.$id);
        $this->assertEquals(302, $client->getResponse()->getStatusCode()); #Den failer på 302??
        $client->request('POST', '/kontrollpanel/foreldrekurs/slett/'.$id);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }




}
