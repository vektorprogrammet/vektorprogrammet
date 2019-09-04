<?php

namespace Tests\AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\Survey;
use Tests\BaseWebTestCase;

class SurveyNotificationControllerTest extends BaseWebTestCase
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


    public function testSurveyNotificationsUrls(){
        $client = $this->createAdminClient();

        $teammembers = $this->em->getRepository('AppBundle:User')->findTeamMembers();
        $teammemberIds = array_map(function($user){ return $user->getId();}, $teammembers);
        $teammemberIds = array_map('strval', $teammemberIds);


        $crawler = $this->goTo("/kontrollpanel/brukergruppesamling/opprett", $client);
        $form = $crawler->selectButton('Lagre')->form();
        $form["user_group_collection[name]"] = "Brukergruppe 4";
        $form["user_group_collection[numberUserGroups]"] = "4";
        $form["user_group_collection[semesters][0]"]->tick();
        $form["user_group_collection[assistantsDepartments][0]"]->tick();
        $form["user_group_collection[assistantBolks][0]"]->tick();
        $form["user_group_collection[assistantBolks][1]"]->tick();
        $form["user_group_collection[users]"] = $teammemberIds;
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertTrue($crawler->filter('td:contains("Brukergruppe 4")')->count() > 0);


        $userGroupCollections = $this->em->getRepository('AppBundle:UserGroupCollection')->findAll();
        $userGroupCollection = array_pop($userGroupCollections);
        $userGroup = $userGroupCollection->getUserGroups()[0];
        $userGroupId = $userGroup->getId();


        $surveyName =  "TestSurveyForNotifications";
        $client = $this->createAdminClient();
        $crawler = $this->goTo('/kontrollpanel/undersokelse/opprett', $client);
        $form = $crawler->selectButton('Lagre')->form();
        $form["survey[name]"] = $surveyName;
        $client->submit($form);
        $client->followRedirect();


        $survey = $this->em->getRepository('AppBundle:Survey')->findByName($surveyName)[0];
        $surveyId = $survey->getId();

        $crawler = $this->goTo("/kontrollpanel/undersokelsevarsel/opprett", $client);
        $form = $crawler->selectButton('Lagre')->form();
        $form["survey_notifier[timeOfNotification][date][year]"] = "2016";
        $form["survey_notifier[timeOfNotification][date][month]"] = "1";
        $form["survey_notifier[timeOfNotification][date][day]"] = "1";
        $form["survey_notifier[usergroups]"]=array((string)$userGroupId);
        $form["survey_notifier[name]"]="TestSurveyNotifier1234";
        $form["survey_notifier[survey]"]=(string)$surveyId;
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertTrue($crawler->filter('td:contains("TestSurveyNotifier1234")')->count()>0);
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
