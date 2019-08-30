<?php

namespace Tests\AppBundle\Controller;

use Tests\BaseWebTestCase;

class SurveyNotifierControllerTest extends BaseWebTestCase
{

    public function testCreateUserGroup()
    {

        $client = $this->createAdminClient();
        $crawler = $this->goTo('/kontrollpanel/brukergruppesamling', $client);
        $this->assertTrue($crawler->filter('td:contains("Brukergruppe 1")')->count() > 0);
        $this->assertTrue( $crawler->filter('td:contains("Brukergruppe 2")')->count() > 0);

        $crawler = $this->goTo("/kontrollpanel/brukergruppesamling/opprett", $client);
        $form = $crawler->selectButton('Lagre')->form();
        $form["user_group_collection[name]"] = "Brukergruppe 3";
        $form["user_group_collection[numberUserGroups]"] = "2";
        $form["user_group_collection[semesters][0]"]->tick();
        $form["user_group_collection[assistantsDepartments][0]"]->tick();
        $form["user_group_collection[assistantBolks][0]"]->tick();
        $form["user_group_collection[assistantBolks][1]"]->tick();
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertTrue($crawler->filter('td:contains("Brukergruppe 3")')->count() > 0);


        $crawler = $this->goTo("/kontrollpanel/brukergruppesamling/opprett", $client);
        $form = $crawler->selectButton('Lagre')->form();
        $form["user_group_collection[name]"] = "Brukergruppe 4";
        $form["user_group_collection[numberUserGroups]"] = "4";
        $form["user_group_collection[semesters][0]"]->tick();
        $form["user_group_collection[assistantsDepartments][0]"]->tick();
        $form["user_group_collection[assistantBolks][0]"]->tick();
        $form["user_group_collection[assistantBolks][1]"]->tick();
        $form["user_group_collection[users]"] = array("100","101", "102", "103", "104", "105");
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertTrue($crawler->filter('td:contains("Brukergruppe 4")')->count() > 0);


        $crawler = $this->goTo("/kontrollpanel/brukergruppesamling/opprett", $client);
        $form = $crawler->selectButton('Lagre')->form();
        $form["user_group_collection[name]"] = "Brukergruppe 5";
        $form["user_group_collection[numberUserGroups]"] = "4";
        $form["user_group_collection[users]"] = array("100","101", "102");
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertEquals(0,$crawler->filter('td:contains("Brukergruppe 5")')->count());

    }

    public function testSurveyNotificationsUrls(){
        $client = $this->createAdminClient();
        $crawler = $this->goTo('/kontrollpanel/undersokelse/opprett', $client);
        $form = $crawler->selectButton('Lagre')->form();
        $form["survey[name]"] = "TestSurveyForNotifications";
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertEquals("/undersokelse/46",
            $crawler->filterXPath(("(//td[contains(text(),'TestSurveyForNotifications')]/../td/a/@href)[1]"))[0]);


    }



}
