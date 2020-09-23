<?php

use Tests\BaseWebTestCase;

class ParentCourseControllerTest extends BaseWebTestCase
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

        $crawler = $client->request('GET', '/kontrollpanel/foreldrekurs/create');
        $form = $crawler->selectButton('Opprett')->form();

        $date = (new \DateTime())->modify('+1day')->format('d.m.Y H:m');

        $form['parent_course[speaker]'] = 'Ola Nordmann';
        $form['parent_course[place]'] = 'Auditorium R5, NTNU Gløshaugen';
        $form['parent_course[link]'] = 'https://use.mazemap.com/#v=1&zlevel=-1&center=10.405274,63.415645&zoom=18&campusid=1&typepois=7&sharepoitype=poi&sharepoi=1949';
        $form['parent_course[date]'] = $date;
        $form['parent_course[information]'] = 'Dette kurset skal handle om hvordan DU som forelder kan bidra på best mulig måte i ditt barns læring!';

        $client->submit($form);

        $crawler = $client->followRedirect();

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
        $course = $this->em->getRepository('AppBundle:ParentCourse')->findOneBy(array('speaker' => $this->speaker));
        $id = $course->getId();

        $client->request('POST', '/kontrollpanel/foreldrekurs/slett/' . $id);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->request('POST', '/kontrollpanel/foreldrekurs/slett/' . $id);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
}
