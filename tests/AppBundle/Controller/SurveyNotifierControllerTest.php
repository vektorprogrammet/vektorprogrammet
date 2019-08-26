<?php

namespace Tests\AppBundle\Controller;

use Tests\BaseWebTestCase;

class SurveyNotifierControllerTest extends BaseWebTestCase
{

    public function testCreateUserGroup()
    {

        $client = $this->createAdminClient();
        $crawler = $this->goTo('/kontrollpanel/brukergruppesamling', $client);
        $this->assertEquals(1, $crawler->filter('td:contains("Brukergruppe 1")')->count());
        $this->assertEquals( 1, $crawler->filter('td:contains("Brukergruppe 2")')->count());
        $this->assertTrue( $crawler->filter('.fa-lock-open")')->count()>0);

        $crawler = $this->goTo("/kontrollpanel/brukergruppesamling/opprett", $client);

        $form = $crawler->selectButton('Lagre')->form();
        $form['createExecutiveBoardMembership[user]']->select(1);


        $form["user_group_collection[name]"] = "Brukergruppe 3";
        $form["user_group_collection[numberUserGroups]"] = "2";
        $form["user_group_collection[semesters]"]->select(0);
        $form["user_group_collection[assistantsDepartments]"]->select(0);
        $form["user_group_collection[assistantBolks]"]->select(0);
        $form["user_group_collection[assistantBolks]"]->select(1);
        $client->submit($form);

        $crawler = $client->followRedirect();
        $this->assertEquals(1, $crawler->filter('td:contains("Brukergruppe 3")')->count());



    }



}
