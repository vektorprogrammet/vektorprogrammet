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
    public function testCreate()
    {
        $client = $this->createAdminClient();

        $speaker = 'CourseToBeAssignedTo';
        $course = $this->em->getRepository('AppBundle:ParentCourse')->findOneBy(array('speaker'=> $speaker));
        $id = $course->getId(); #Gir meg ikke iden, men gir meg entryen i databasen!

        $crawler = $client->request('GET', '/kontrollpanel/foreldrekurs/pamelding/68');

        $form = $crawler->selectButton('Meld på!')->form();

        $form['parent_assignment[navn]'] = 'Ola Nordmann';
        $form['parent_assignment[epost]'] = 'ola@nordmann.no';
        #Må jeg teste at setCourse fungerer i selve controlleren også? (altså uten formen)

        $client->submit($form);
        /*
        $crawler = $client->followRedirect();
        $parentAssignmentAdminAfter = $crawler->filter('td:contains("Ola Nordmann")')->count();
        dump($parentAssignmentAdminAfter);
        dump($crawler);
        $this->assertEquals(1, $parentAssignmentAdminAfter);
        */


    }


}
