<?php

namespace Tests\AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\Survey;
use Tests\BaseWebTestCase;

class UsergroupControllerTest extends BaseWebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    private $client;
    public function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->client = $this->createAdminClient();
    }

    public function testCreateUserGroup()
    {

        $crawler = $this->goTo('/kontrollpanel/brukergruppesamling', $this->client);
        $this->assertTrue($crawler->filter('td:contains("Brukergruppe 1")')->count() > 0);
        $this->assertTrue( $crawler->filter('td:contains("Brukergruppe 2")')->count() > 0);

        $crawler = $this->goTo("/kontrollpanel/brukergruppesamling/opprett", $this->client);
        $form = $crawler->selectButton('Lagre')->form();
        $form["user_group_collection[name]"] = "Brukergruppe 3";
        $form["user_group_collection[numberUserGroups]"] = "2";
        $form["user_group_collection[semesters][0]"]->tick();
        $form["user_group_collection[assistantsDepartments][0]"]->tick();
        $form["user_group_collection[assistantBolks][0]"]->tick();
        $form["user_group_collection[assistantBolks][1]"]->tick();
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $this->assertTrue($crawler->filter('td:contains("Brukergruppe 3")')->count() > 0);


        $teammemberIds = $this->getTeamMemberIds();

        $crawler = $this->goTo("/kontrollpanel/brukergruppesamling/opprett", $this->client);
        $form = $crawler->selectButton('Lagre')->form();
        $form["user_group_collection[name]"] = "Brukergruppe 4";
        $form["user_group_collection[numberUserGroups]"] = "4";
        $form["user_group_collection[semesters][0]"]->tick();
        $form["user_group_collection[assistantsDepartments][0]"]->tick();
        $form["user_group_collection[assistantBolks][0]"]->tick();
        $form["user_group_collection[assistantBolks][1]"]->tick();
        $form["user_group_collection[users]"] = $teammemberIds;
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $this->assertTrue($crawler->filter('td:contains("Brukergruppe 4")')->count() > 0);

    }

    public function testInvalidNumberOfUserGroups()
    {
        $teammemberIds = $this->getTeamMemberIds();
        $crawler = $this->goTo("/kontrollpanel/brukergruppesamling/opprett", $this->client);
        $form = $crawler->selectButton('Lagre')->form();
        $form["user_group_collection[name]"] = "Brukergruppe 5";
        $form["user_group_collection[numberUserGroups]"] = (string) (sizeof($teammemberIds)+1);
        $form["user_group_collection[users]"] =  $teammemberIds;
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $this->assertEquals(0,$crawler->filter('td:contains("Brukergruppe 5")')->count());
    }


    public function testCorrectAssistantSelection(){
        $crawler = $this->goTo("/kontrollpanel/brukergruppesamling/opprett", $this->client);
        $form = $crawler->selectButton('Lagre')->form();
        $form["user_group_collection[name]"] = "Brukergruppe 6";
        $form["user_group_collection[numberUserGroups]"] = "3";
        $form["user_group_collection[semesters][0]"]->tick();
        $form["user_group_collection[assistantsDepartments][0]"]->tick();
        $form["user_group_collection[assistantBolks][0]"]->tick();
        $this->client->submit($form);

        $userGroupCollections = $this->em->getRepository('AppBundle:UserGroupCollection')->findAll();
        $userGroupCollection = array_pop($userGroupCollections);

        $this->assertEquals(3, $userGroupCollection->getNumberUserGroups());

        $department = $this->em->getRepository('AppBundle:Department')->findDepartmentByShortName("NTNU");
        $semester = $this->em->getRepository('AppBundle:Semester')->findCurrentSemester();

        $userIdsInDatabaseTemporary = $this->em
            ->getRepository('AppBundle:User')
            ->findUsersWithAssistantHistoryInDepartmentAndSemester($department, $semester);

        $userIdsInDatabase = array();
        foreach ($userIdsInDatabaseTemporary as $user){
            $ah = $this->em->getRepository("AppBundle:AssistantHistory")->findMostRecentByUser($user)[0];
            if($ah->getBolk()==="Bolk 2"){
                array_push($userIdsInDatabase, $user->getId());
            }
        }


        $numUsersInUsergroup = $userGroupCollection->getNumberTotalUsers();
        $this->assertEquals(sizeof($userIdsInDatabase), $numUsersInUsergroup);

        $userGroupUsersArrayCollection = $userGroupCollection->getUsers();

        foreach ($userGroupUsersArrayCollection as $user){
            $this->assertTrue(in_array($user->getId(), $userIdsInDatabase));
        }
    }


    public function testEditCorrectAssistantSelection(){
        $userGroupCollections = $this->em->getRepository('AppBundle:UserGroupCollection')->findAll();
        $userGroupCollection = array_pop($userGroupCollections);

        $crawler = $this->goTo("/kontrollpanel/brukergruppesamling/rediger/".$userGroupCollection->getId(), $this->client);
        $form = $crawler->selectButton('Lagre')->form();
        $form["user_group_collection[name]"] = "Brukergruppe 7";
        $form["user_group_collection[numberUserGroups]"] = "1";
        $form["user_group_collection[semesters][0]"]->tick();
        $form["user_group_collection[assistantsDepartments][0]"]->tick();
        $form["user_group_collection[assistantBolks][1]"]->tick();
        $form["user_group_collection[assistantBolks][2]"]->tick();
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $this->assertEquals(0,$crawler->filter('td:contains("Brukergruppe 6")')->count());
        $this->assertEquals(2,$crawler->filter('td:contains("Brukergruppe 7")')->count());


        $department = $this->em->getRepository('AppBundle:Department')->findDepartmentByShortName("NTNU");
        $semester = $this->em->getRepository('AppBundle:Semester')->findCurrentSemester();

        $userIdsInDatabaseTemporary = $this->em
            ->getRepository('AppBundle:User')
            ->findUsersWithAssistantHistoryInDepartmentAndSemester($department, $semester);

        $userIdsInDatabase = array();
        foreach ($userIdsInDatabaseTemporary as $user){
            $ah = $this->em->getRepository("AppBundle:AssistantHistory")->findMostRecentByUser($user)[0];
            if($ah->getBolk()!=="Bolk 2"){
                array_push($userIdsInDatabase, $user->getId());
            }
        }


        $numUsersInUsergroup = $userGroupCollection->getNumberTotalUsers();
        $this->assertEquals(sizeof($userIdsInDatabase), $numUsersInUsergroup);

        $userGroupUsersArrayCollection = $userGroupCollection->getUsers();

        foreach ($userGroupUsersArrayCollection as $user){
            $this->assertTrue(in_array($user->getId(), $userIdsInDatabase));
        }
    }

    public function testDeleteUserGroup(){
        $crawler = $this->goTo("/kontrollpanel/brukergruppesamling/opprett", $this->client);
        $form = $crawler->selectButton('Lagre')->form();
        $form["user_group_collection[name]"] = "Brukergruppe 10";
        $form["user_group_collection[numberUserGroups]"] = "2";
        $form["user_group_collection[semesters][0]"]->tick();
        $form["user_group_collection[assistantsDepartments][0]"]->tick();
        $form["user_group_collection[assistantBolks][0]"]->tick();
        $form["user_group_collection[assistantBolks][1]"]->tick();
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $userGroupCollections = $this->em->getRepository('AppBundle:UserGroupCollection')->findAll();
        $userGroupCollection = array_pop($userGroupCollections);
        $this->client->request('POST', "/kontrollpanel/brukergruppesamling/slett/".$userGroupCollection->getId());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode()); // Successful if redirected
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

    private function getTeamMemberIds(){
        $teammembers = $this->em->getRepository('AppBundle:User')->findTeamMembers();
        $teammemberIds = array_map(function($user){ return $user->getId();}, $teammembers);
        return array_map('strval', $teammemberIds);
    }


}
