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
        $form["user_group_collection[semesters]"]->array(11);
        $form["user_group_collection[assistantsDepartments]"]->select(0);
        $form["user_group_collection[assistantBolks]"]->select(0);
        $form["user_group_collection[assistantBolks]"]->select(1);
        $client->submit($form);

        $crawler = $client->followRedirect();
        $this->assertEquals(1, $crawler->filter('td:contains("Brukergruppe 3")')->count());



    }



}
