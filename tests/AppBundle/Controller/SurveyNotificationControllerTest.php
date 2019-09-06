<?php

namespace Tests\AppBundle\Controller;

use Tests\BaseWebTestCase;

class SurveyNotificationControllerTest extends BaseWebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    private $client;
    private $userGroups;
    private $survey;
    public function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->client = $this->createAdminClient();

        $teammembers = $this->em->getRepository('AppBundle:User')->findTeamMembers();
        $teammemberIds = array_map(function($user){ return $user->getId();}, $teammembers);
        $teammemberIds = array_map('strval', $teammemberIds);

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
        $this->userGroups = $this->em->getRepository('AppBundle:UserGroup')->findAll();


        $surveyName =  "TestSurveyForNotifications";
        $this->client = $this->createAdminClient();
        $crawler = $this->goTo('/kontrollpanel/undersokelse/opprett', $this->client);
        $form = $crawler->selectButton('Lagre')->form();
        $form["survey[name]"] = $surveyName;
        $this->client->submit($form);
        $this->client->followRedirect();
        $this->survey = $this->em->getRepository('AppBundle:Survey')->findByName($surveyName)[0];



    }


    public function testCreateSurveyNotifier(){
        $userGroupIds = array_map(function($userGroup){ return $userGroup->getId();}, $this->userGroups);
        $userGroupIds = array_map('strval', $userGroupIds);
        $surveyId = (string)$this->survey->getId();



        $crawler = $this->goTo("/kontrollpanel/undersokelsevarsel/opprett", $this->client);
        $form = $crawler->selectButton('Lagre')->form();
        $form["survey_notifier[timeOfNotification][date][year]"] = "2016";
        $form["survey_notifier[timeOfNotification][date][month]"] = "1";
        $form["survey_notifier[timeOfNotification][date][day]"] = "1";
        $form["survey_notifier[usergroups]"] = $userGroupIds;
        $form["survey_notifier[name]"]="TestSurveyNotifier1234";
        $form["survey_notifier[survey]"] = $surveyId;
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $this->assertTrue($crawler->filter('td:contains("TestSurveyNotifier1234")')->count()>0);

        $crawler = $this->goTo("/kontrollpanel/undersokelsevarsel/opprett", $this->client);
        $form = $crawler->selectButton('Lagre')->form();
        $form["survey_notifier[timeOfNotification][date][year]"] = "2016";
        $form["survey_notifier[timeOfNotification][date][month]"] = "1";
        $form["survey_notifier[timeOfNotification][date][day]"] = "1";
        $form["survey_notifier[usergroups]"] = array($userGroupIds[0]);
        $form["survey_notifier[name]"]="TestSurveyNotifier12345";
        $form["survey_notifier[survey]"] = $surveyId;
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $this->assertTrue($crawler->filter('td:contains("TestSurveyNotifier12345")')->count()>0);

    }

    public function testEditSurveyNotifier()
    {
        $userGroupIds = array_map(function($userGroup){ return $userGroup->getId();}, $this->userGroups);
        $userGroupIds = array_map('strval', $userGroupIds);
        $surveyId = (string)$this->survey->getId();



        $crawler = $this->goTo("/kontrollpanel/undersokelsevarsel/opprett", $this->client);
        $form = $crawler->selectButton('Lagre')->form();
        $form["survey_notifier[timeOfNotification][date][year]"] = "2016";
        $form["survey_notifier[timeOfNotification][date][month]"] = "1";
        $form["survey_notifier[timeOfNotification][date][day]"] = "1";
        $form["survey_notifier[usergroups]"] = $userGroupIds;
        $form["survey_notifier[name]"]="TestEdit1";
        $form["survey_notifier[survey]"] = $surveyId;
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $this->assertTrue($crawler->filter('td:contains("TestEdit1")')->count()>0);

        $surveyNotifier = $this->em->getRepository('AppBundle:SurveyNotificationCollection')->findOneBy([
            'name' => "TestEdit1"
        ]);

        $crawler = $this->goTo("/kontrollpanel/undersokelsevarsel/rediger/".$surveyNotifier->getId(), $this->client);
        $form = $crawler->selectButton('Lagre')->form();
        $form["survey_notifier[name]"]="TestEdit2";
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $this->assertTrue($crawler->filter('td:contains("TestEdit2")')->count()>0);
        $this->assertEquals(0,$crawler->filter('td:contains("TestEdit1")')->count());

        $this->client->request('POST','/kontrollpanel/undersokelsevarsel/send/'.$surveyNotifier->getId());

        $crawler = $this->goTo("/kontrollpanel/undersokelsevarsel/rediger/".$surveyNotifier->getId(), $this->client);
        $form = $crawler->selectButton('Lagre')->form();
        $form["survey_notifier[name]"]="TestEdit3";
        $this->client->submit($form);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());







    }

    public function testDeleteSurveyNotifier(){
        $userGroupIds = array_map(function($userGroup){ return $userGroup->getId();}, $this->userGroups);
        $userGroupIds = array_map('strval', $userGroupIds);
        $surveyId = (string)$this->survey->getId();



        $crawler = $this->goTo("/kontrollpanel/undersokelsevarsel/opprett", $this->client);
        $form = $crawler->selectButton('Lagre')->form();
        $form["survey_notifier[timeOfNotification][date][year]"] = "2016";
        $form["survey_notifier[timeOfNotification][date][month]"] = "1";
        $form["survey_notifier[timeOfNotification][date][day]"] = "1";
        $form["survey_notifier[usergroups]"] = $userGroupIds;
        $form["survey_notifier[name]"]="TestDelete1";
        $form["survey_notifier[survey]"] = $surveyId;
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $this->assertTrue($crawler->filter('td:contains("TestDelete1")')->count()>0);

        $surveyNotifier = $this->em->getRepository('AppBundle:SurveyNotificationCollection')->findOneBy([
            'name' => "TestDelete1"
        ]);

        $this->client->request('POST','/kontrollpanel/undersokelsevarsel/slett/'.$surveyNotifier->getId());
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());


        $crawler = $this->goTo("/kontrollpanel/undersokelsevarsel/opprett", $this->client);
        $form = $crawler->selectButton('Lagre')->form();
        $form["survey_notifier[timeOfNotification][date][year]"] = "2016";
        $form["survey_notifier[timeOfNotification][date][month]"] = "1";
        $form["survey_notifier[timeOfNotification][date][day]"] = "1";
        $form["survey_notifier[usergroups]"] = $userGroupIds;
        $form["survey_notifier[name]"]="TestDelete2";
        $form["survey_notifier[survey]"] = $surveyId;
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $this->assertTrue($crawler->filter('td:contains("TestDelete12)')->count()>0);

        $surveyNotifier = $this->em->getRepository('AppBundle:SurveyNotificationCollection')->findOneBy([
            'name' => "TestDelete2"
        ]);

        $this->client->request('POST','/kontrollpanel/undersokelsevarsel/send/'.$surveyNotifier->getId());
        $this->client->request('POST','/kontrollpanel/undersokelsevarsel/slett/'.$surveyNotifier->getId());
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());








    }

    public function testSurveyNotificationContent(){

    }

    public function testSurveyNotificationClick(){
    //anon client
    }



    public function testSendSurveyNotifier(){
        $userGroupIds = array_map(function($userGroup){ return $userGroup->getId();}, $this->userGroups);
        $userGroupIds = array_map('strval', $userGroupIds);
        $surveyId = (string)$this->survey->getId();

        $crawler = $this->goTo("/kontrollpanel/undersokelsevarsel/opprett", $this->client);
        $form = $crawler->selectButton('Lagre')->form();
        $form["survey_notifier[timeOfNotification][date][year]"] = "2016";
        $form["survey_notifier[timeOfNotification][date][month]"] = "1";
        $form["survey_notifier[timeOfNotification][date][day]"] = "1";
        $form["survey_notifier[usergroups]"] = array($userGroupIds[0]);
        $form["survey_notifier[name]"]="TestSurveyNotifier123456";
        $form["survey_notifier[survey]"] = $surveyId;
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $this->assertTrue($crawler->filter('td:contains("TestSurveyNotifier123456")')->count()>0);


        $surveyNotifier = $this->em->getRepository('AppBundle:SurveyNotificationCollection')->findAll()[0];

        $this->client->request('POST','/kontrollpanel/undersokelsevarsel/send/'.$surveyNotifier->getId());
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode()); // Successful if redirected

        $nextYear = new \DateTime();
        $nextYear->add(new \DateInterval("P1Y"));
        $nextYear = $nextYear->format("Y");

        $crawler = $this->goTo("/kontrollpanel/undersokelsevarsel/opprett", $this->client);
        $form = $crawler->selectButton('Lagre')->form();
        $form["survey_notifier[timeOfNotification][date][year]"] = $nextYear;
        $form["survey_notifier[timeOfNotification][date][month]"] = "1";
        $form["survey_notifier[timeOfNotification][date][day]"] = "1";
        $form["survey_notifier[usergroups]"] = $userGroupIds;
        $form["survey_notifier[name]"]="TestSurveyNotifier1234567";
        $form["survey_notifier[survey]"] = $surveyId;
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $this->assertTrue($crawler->filter('td:contains("TestSurveyNotifier1234567")')->count()>0);


        $surveyNotifier = $this->em->getRepository('AppBundle:SurveyNotificationCollection')->findAll()[0];

        $this->client->request('POST','/kontrollpanel/undersokelsevarsel/send/'.$surveyNotifier->getId());
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }



    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }



}
