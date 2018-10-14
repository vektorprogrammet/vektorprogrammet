<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;

class SchoolAdminControllerTest extends BaseWebTestCase
{
    public function testCreateSchoolForDepartment()
    {

        // ADMIN
        $client = $this->createAdminClient();

        $crawler = $client->request('GET', '/kontrollpanel/skoleadmin/opprett/1');

        $form = $crawler->selectButton('Opprett')->form();

        // Change the value of a field
        $form['createSchool[name]'] = 'test1';
        $form['createSchool[contactPerson]'] = 'person1';
        $form['createSchool[phone]'] = '12312312';
        $form['createSchool[email]'] = 'test1@mail.com';

        // submit the form
        $client->submit($form);

        // Assert a specific 302 status code
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        // Assert that the response is the correct redirect
        $this->assertTrue($client->getResponse()->isRedirect('/kontrollpanel/skoleadmin'));
    }

    public function testUpdateSchool()
    {

        // ADMIN
        $client = $this->createAdminClient();

        $crawler = $client->request('GET', '/kontrollpanel/skoleadmin/avdeling/1');

        // Find a link and click it
        $link = $crawler->selectLink('Rediger')->eq(1)->link();
        $crawler = $client->click($link);

        $form = $crawler->selectButton('Opprett')->form();

        // Change the value of a field
        $form['createSchool[name]'] = 'test2';
        $form['createSchool[contactPerson]'] = 'person2';
        $form['createSchool[phone]'] = '99912399';
        $form['createSchool[email]'] = 'test2@mail.com';

        // submit the form
        $client->submit($form);

        // Assert a specific 302 status code
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        // Assert that the response is the correct redirect
        $this->assertTrue($client->getResponse()->isRedirect('/kontrollpanel/skoleadmin'));
    }

    public function testShowSchoolsByDepartment()
    {

        // ADMIN
        $client = $this->createAdminClient();

        $crawler = $client->request('GET', '/kontrollpanel/skoleadmin/avdeling/1');

        // Assert that we have the correct amount of data
        $this->assertEquals(1, $crawler->filter('h2:contains("Skoler i Trondheim")')->count());
        $this->assertEquals(1, $crawler->filter('a:contains("Gimse")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Per Olsen")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Per@mail.com")')->count());

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $crawler = $client->request('GET', '/kontrollpanel/skoleadmin/avdeling/2');

        // Assert that we have the correct amount of data
        $this->assertEquals(1, $crawler->filter('h2:contains("Skoler i Bergen")')->count());
        $this->assertEquals(1, $crawler->filter('a:contains("Blussuvoll")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Kari Johansen")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("kari@mail.com")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Internasjonal")')->count());
    }

    public function testShowUsersByDepartment()
    {

        // TEAM
        $client = $this->createTeamMemberClient();

        $crawler = $client->request('GET', '/kontrollpanel/skoleadmin/brukere');

        // Assert that we have the correct amount of data
        $this->assertEquals(1, $crawler->filter('h2:contains("Tildel skole")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Persdatter Ødegaard")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Brenna Eskeland")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Ravnå")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Myrvoll-Nilsen")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Rasdal Håland")')->count());
    }

    public function testShowUsersByDepartmentSuperadmin()
    {
        $client = $this->createAdminClient();

        $crawler = $client->request('GET', '/kontrollpanel/skoleadmin/avdeling/2');

        // Assert that we have the correct amount of data
        $this->assertEquals(1, $crawler->filter('h2:contains("Skoler i Bergen")')->count());
        $this->assertEquals(1, $crawler->filter('a:contains("Blussuvoll")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Kari Johansen")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("kari@mail.com")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Internasjonal")')->count());

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $crawler = $client->request('GET', '/kontrollpanel/skoleadmin/avdeling/1');

        // Assert that we have the correct amount of data
        $this->assertEquals(1, $crawler->filter('h2:contains("Skoler i Trondheim")')->count());
        $this->assertEquals(1, $crawler->filter('a:contains("Gimse")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Per Olsen")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Per@mail.com")')->count());
    }

    public function testDelegateSchoolToUser()
    {
        $client = $this->createAdminClient();

        $crawler = $client->request('GET', '/kontrollpanel/skoleadmin/brukere/avdeling/1');

        // Assert that we have the correct amount of data
        $this->assertEquals(1, $crawler->filter('h2:contains("Tildel skole")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Reidun")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Persdatter Ødegaard")')->count());

        // Find a link and click it
        $link = $crawler->selectLink('Tildel skole')->eq(1)->link();
        $crawler = $client->click($link);

        $form = $crawler->selectButton('Opprett')->form();

        // Change the value of a field
        $form['createAssistantHistory[Semester]']->select(1);
        $form['createAssistantHistory[workdays]']->select('4');
        $form['createAssistantHistory[School]']->select(2);
        $form['createAssistantHistory[bolk]']->select('Bolk 2');
        $form['createAssistantHistory[day]']->select('Onsdag');

        // submit the form
        $client->submit($form);

        // Assert a specific 302 status code
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        // Follow the redirect
        $client->followRedirect();

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $client = $this->createTeamLeaderClient();

        $crawler = $client->request('GET', '/kontrollpanel/skoleadmin/brukere/avdeling/1');

        // Assert that we have the correct amount of data
        $this->assertEquals(1, $crawler->filter('h2:contains("Tildel skole")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Reidun")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Persdatter Ødegaard")')->count());

        // Find a link and click it
        $link = $crawler->selectLink('Tildel skole')->eq(1)->link();
        $crawler = $client->click($link);

        $form = $crawler->selectButton('Opprett')->form();

        // Change the value of a field
        $form['createAssistantHistory[Semester]']->select(1);
        $form['createAssistantHistory[workdays]']->select('4');
        $form['createAssistantHistory[School]']->select(2);
        $form['createAssistantHistory[bolk]']->select('Bolk 2');
        $form['createAssistantHistory[day]']->select('Onsdag');

        // submit the form
        $client->submit($form);

        // Assert a specific 302 status code
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        // Follow the redirect
        $client->followRedirect();

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testShowSpecificSchool()
    {

        // ADMIN
        $client = $this->createAdminClient();

        $crawler = $client->request('GET', '/kontrollpanel/skoleadmin');

        // Find a link and click it
        $link = $crawler->selectLink('Selsbakk')->link();
        $crawler = $client->click($link);

        // Assert that we have the correct amount of data
        $this->assertEquals(1, $crawler->filter('h2:contains("Selsbakk")')->count());

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('GET', '/kontrollpanel/skole/2');

        // Assert that the response status code is 2xx
        $this->assertTrue($client->getResponse()->isSuccessful());

        // TEAM
        $client = $this->createTeamMemberClient();

        $crawler = $client->request('GET', '/kontrollpanel/skoleadmin');

        // Find a link and click it
        $link = $crawler->selectLink('Gimse')->link();
        $crawler = $client->click($link);

        // Assert that we have the correct amount of data
        $this->assertEquals(1, $crawler->filter('h2:contains("Gimse")')->count());
    }

    public function testShow()
    {
        $client = $this->createAdminClient();

        $crawler = $client->request('GET', '/kontrollpanel/skoleadmin');

        // Assert that we have the correct amount of data
        $this->assertEquals(1, $crawler->filter('h2:contains("Skoler")')->count());
        $this->assertEquals(1, $crawler->filter('a:contains("Selsbakk")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Vibeke Hansen")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Vibeke@mail.com")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("22386722")')->count());

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // TEAM
        $client = $this->createTeamMemberClient();

        $crawler = $client->request('GET', '/kontrollpanel/skoleadmin');

        // Assert that we have the correct amount of data
        $this->assertEquals(1, $crawler->filter('h2:contains("Skoler")')->count());
        $this->assertEquals(1, $crawler->filter('a:contains("Gimse")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Per Olsen")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Per@mail.com")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("99887722")')->count());
    }
}
