<?php

namespace Tests\AppBundle\Controller;

use Tests\BaseWebTestCase;

class ParticipantHistoryControllerTest extends BaseWebTestCase
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

    public function testIndex()
    {
        $client = $this->createAdminClient();

        $crawler = $client->request('GET', '/kontrollpanel/deltakerhistorikk');

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h2:contains("Assistenter")')->count());

        // Assert that we have the correct data
        $this->assertContains('Petter Johansen', $client->getResponse()->getContent());
        $this->assertContains('petter@stud.ntnu.no', $client->getResponse()->getContent());
        $this->assertContains('Hovedstyret', $client->getResponse()->getContent());
        $this->assertContains('NTNU', $client->getResponse()->getContent());
        $this->assertContains('Bolk 2', $client->getResponse()->getContent());
        $this->assertContains('Onsdag', $client->getResponse()->getContent());
        $this->assertContains('Gimse', $client->getResponse()->getContent());


        // Count from database
        $bolkCount = 0;
        $dayCount = 0;
        $schoolCount = 0;
        $department = $this->em->getRepository('AppBundle:Department')->findDepartmentByShortName("NTNU");
        $semester = $this->em->getRepository('AppBundle:Semester')->findCurrentSemester();
        $assistantHistoriesFromDepartment = $this->em->getRepository("AppBundle:AssistantHistory")->findByDepartmentAndSemester($department, $semester);
        foreach ($assistantHistoriesFromDepartment as $ah){
            if($ah->getBolk()==="Bolk 2"){
                $bolkCount += 1;
            }

            if($ah->getSchool()->getName() ==="Gimse"){
                $schoolCount += 1;
            }

            if($ah->getDay() ==="Onsdag"){
                $dayCount += 1;
            }
        }


        // Check the count for the different variables
        // Assume Petter only has one assistant history  in department and semester
        $this->assertEquals(1, $crawler->filter('td a:contains("Petter Johansen")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("petter@stud.ntnu.no")')->count());

        // Check if count matches database assistant histories
        $this->assertEquals($bolkCount, $crawler->filter('td:contains("Bolk 2")')->count());
        $this->assertEquals($dayCount, $crawler->filter('td:contains("Onsdag")')->count());
        $this->assertEquals($schoolCount, $crawler->filter('td:contains("Gimse")')->count());

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
