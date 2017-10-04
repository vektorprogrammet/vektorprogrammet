<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;

class TeamAdminControllerTest extends BaseWebTestCase
{
    public function testCreatePosition()
    {
        // ADMIN
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'petjo',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/teamadmin');

        // Find a link and click it
        $link = $crawler->selectLink('Stillinger')->eq(0)->link();
        $crawler = $client->click($link);

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Stillinger")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Leder")')->count());

        // Find a link and click it
        $link = $crawler->selectLink('Opprett stilling')->eq(0)->link();
        $crawler = $client->click($link);

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Opprett stilling")')->count());

        $form = $crawler->selectButton('Lagre')->form();

        // Change the value of a field
        $form['createPosition[name]'] = 'Nestleder1';

        // submit the form
        $crawler = $client->submit($form);

        // Follow the redirect
        $crawler = $client->followRedirect();

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Stillinger")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Leder")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Nestleder1")')->count());

        // USER
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'assistent',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/teamadmin/opprett/stilling');

        // Assert that the response is a redirect
        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        // TEAM
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'team',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/teamadmin/opprett/stilling');

        // Assert that the response is a redirect
        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        $crawler = $client->request('GET', '/kontrollpanel/teamadmin/opprett/stilling');

        // Assert that the response is a redirect
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testEditPosition()
    {
        // ADMIN
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'petjo',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/teamadmin');

        // Find a link and click it
        $link = $crawler->selectLink('Stillinger')->eq(0)->link();
        $crawler = $client->click($link);

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Stillinger")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Leder")')->count());

        // Find a link and click it
        $link = $crawler->selectLink('Rediger')->eq(1)->link();
        $crawler = $client->click($link);

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Opprett stilling")')->count());

        $form = $crawler->selectButton('Lagre')->form();

        // Change the value of a field
        $form['createPosition[name]'] = 'Nestleder2';

        // submit the form
        $crawler = $client->submit($form);

        // Follow the redirect
        $crawler = $client->followRedirect();

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Stillinger")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Leder")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Nestleder2")')->count());
        $this->assertEquals(0, $crawler->filter('td:contains("Nestleder1")')->count());
    }

    public function testShowPositions()
    {

        // ADMIN
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'petjo',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/teamadmin');

        // Find a link and click it
        $link = $crawler->selectLink('Stillinger')->eq(0)->link();
        $crawler = $client->click($link);

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Stillinger")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Leder")')->count());

        // USER
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'assistent',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/teamadmin/opprett/stilling');

        // Assert that the response is a redirect
        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        // TEAM
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'team',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/teamadmin/stillinger');

        // Assert that the response is a redirect
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testCreateTeamForDepartment()
    {
        // ADMIN
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'petjo',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/teamadmin/avdeling/opprett/1');

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Opprett team")')->count());

        $form = $crawler->selectButton('Lagre')->form();

        // Change the value of a field
        $form['createTeam[name]'] = 'testteam1';

        // submit the form
        $client->submit($form);

        $crawler = $client->followRedirect();

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Team")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("testteam1")')->count());

        // USER
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'assistent',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/teamadmin/avdeling/opprett/2');

        // Assert that the request is denied
        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        // TEAM
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'team',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/teamadmin/avdeling/opprett/2');

        // Assert that the request is denied
        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        $crawler = $client->request('GET', '/kontrollpanel/teamadmin/avdeling/opprett/1');

        // Assert that the response is a redirect
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testUpdateTeam()
    {

        // ADMIN
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'petjo',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/teamadmin');

        // Find a link and click it
        $link = $crawler->selectLink('Rediger')->eq(1)->link();
        $crawler = $client->click($link);

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Oppdater team")')->count());

        $form = $crawler->selectButton('Lagre')->form();

        // Change the value of a field
        $form['createTeam[name]'] = 'testteam2';

        // submit the form
        $client->submit($form);

        $crawler = $client->followRedirect();

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Tea")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("testteam2")')->count());
        $this->assertEquals(0, $crawler->filter('td:contains("testteam1")')->count());
    }

    public function testAddUserToTeam()
    {
        // ADMIN
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'petjo',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/teamadmin/team/2');

        // Find a link and click it
        $link = $crawler->selectLink('Legg til bruker')->eq(0)->link();
        $crawler = $client->click($link);

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Opprett arbeidshistorie")')->count());

        $form = $crawler->selectButton('Opprett')->form();

        // Change the value of a field
        $form['createWorkHistory[user]']->select(1);
        $form['createWorkHistory[position]']->select(1);
        $form['createWorkHistory[startSemester]']->select(1);

        $client->submit($form);

        $crawler = $client->followRedirect();

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("IT")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Petter")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Johansen")')->count());

        // USER
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'assistent',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/teamadmin/team/nytt_medlem/3');

        // Assert that the response is a redirect
        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        // TEAM
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'team',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/teamadmin/team/nytt_medlem/3');

        // Assert that the response is a redirect
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testShowTeamsByDepartment()
    {

        // ADMIN
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'petjo',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/teamadmin/avdeling/1');

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Team")')->count());

        // Check the count for the different variables
        $this->assertEquals(1, $crawler->filter('a:contains("Styret")')->count());
        $this->assertEquals(1, $crawler->filter('a:contains("IT")')->count());

        // USER
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'assistent',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/teamadmin/avdeling/1');

        // Assert that the response is a redirect
        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        // TEAM
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'team',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/teamadmin/avdeling/1');

        // Assert that the response is a redirect
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testShowSpecificTeam()
    {

        // ADMIN
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'petjo',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/teamadmin');

        // Find a link and click it
        $link = $crawler->selectLink('IT')->eq(0)->link();
        $crawler = $client->click($link);

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("IT")')->count());

        // TEAM
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'team',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/teamadmin');

        // Find a link and click it
        $link = $crawler->selectLink('Styret')->eq(0)->link();
        $crawler = $client->click($link);

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Styret")')->count());

        // Check the count for the different variables
        $this->assertEquals(1, $crawler->filter('td:contains("Petter")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Johansen")')->count());
        $this->assertEquals(2, $crawler->filter('td:contains("Thomas")')->count());
        $this->assertEquals(2, $crawler->filter('td:contains("Alm")')->count());

        // USER
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'assistent',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/teamadmin/team/1');

        // Assert that the response is a redirect
        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        // TEAM
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'team',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/teamadmin/team/2');

        // Assert that the response is a redirect
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testShow()
    {

        // TEAM
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'team',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/teamadmin');

        // Check the count for the different variables
        $this->assertEquals(1, $crawler->filter('h1:contains("Team")')->count());
        $this->assertEquals(1, $crawler->filter('a:contains("Styret")')->count());
        $this->assertEquals(1, $crawler->filter('a:contains("IT")')->count());

        // USER
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'assistent',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/teamadmin');

        // Assert that the response is a redirect
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }
}
