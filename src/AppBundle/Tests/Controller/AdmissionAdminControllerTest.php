<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;

class AdmissionAdminControllerTest extends BaseWebTestCase
{
    public function testShowAsTeamMember()
    {
        $crawler = $this->teamMemberGoTo('/kontrollpanel/opptakadmin');

        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());
        $this->assertEquals(0, $crawler->filter('a.button:contains("Ny søker")')->count());
        $this->assertEquals(0, $crawler->filter('td>a.button:contains("Slett")')->count());
        $this->assertEquals(0, $crawler->filter('td>a.button:contains("Fordel")')->count());
    }

    public function testShowAsTeamLeader()
    {
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/opptakadmin');

        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());
        $this->assertEquals(3, $crawler->filter('a:contains("Avdeling")')->count());
        $this->assertEquals(3, $crawler->filter('a:contains("Semester")')->count());
        $this->assertEquals(0, $crawler->filter('td>a:contains("Slett")')->count());
        $this->assertEquals(4, $crawler->filter('a:contains("Fordel")')->count());
    }

    public function testShowAsAdmin()
    {
        $crawler = $this->adminGoTo('/kontrollpanel/opptakadmin');

        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());
        $this->assertEquals(3, $crawler->filter('a:contains("Avdeling")')->count());
        $this->assertEquals(3, $crawler->filter('a:contains("Semester")')->count());
        $this->assertEquals(3, $crawler->filter('td>a:contains("Slett")')->count());
        $this->assertEquals(3, $crawler->filter('td>a:contains("Fordel")')->count());
    }
    
    public function testAssignedAsTeamMember()
    {
        $crawler = $this->teamMemberGoTo('/kontrollpanel/opptakadmin?status=assigned');

        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());
        $this->assertEquals(0, $crawler->filter('td>a:contains("Sett opp")')->count());
        $this->assertEquals(0, $crawler->filter('td>a:contains("Intervju")')->count());
    }

    public function testAssignedAsTeamLeader()
    {
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/opptakadmin?status=assigned');

        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());
        $this->assertEquals(3, $crawler->filter('a:contains("Avdeling")')->count());
        $this->assertEquals(3, $crawler->filter('a:contains("Semester")')->count());
        $this->assertEquals(2, $crawler->filter('td>a:contains("Sett opp")')->count());
        $this->assertEquals(2, $crawler->filter('td>a:contains("Intervju")')->count());
    }

    public function testAssignedAsAdmin()
    {
        $crawler = $this->adminGoTo('/kontrollpanel/opptakadmin?status=assigned');

        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());
        $this->assertEquals(3, $crawler->filter('a:contains("Avdeling")')->count());
        $this->assertEquals(3, $crawler->filter('a:contains("Semester")')->count());
        $this->assertEquals(2, $crawler->filter('td>a:contains("Sett opp")')->count());
        $this->assertEquals(2, $crawler->filter('td>a:contains("Intervju")')->count());
    }
    
    public function testInterviewedAsTeamMember()
    {
        $crawler = $this->teamMemberGoTo('/kontrollpanel/opptakadmin?status=interviewed');

        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());
        $this->assertEquals(0, $crawler->filter('td>a:contains("Les intervju")')->count());
        $this->assertEquals(0, $crawler->filter('td>a:contains("Slett")')->count());
    }

    public function testInterviewedAsTeamLeader()
    {
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/opptakadmin?status=interviewed');

        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());
        $this->assertEquals(3, $crawler->filter('a:contains("Avdeling")')->count());
        $this->assertEquals(3, $crawler->filter('a:contains("Semester")')->count());
        $this->assertEquals(0, $crawler->filter('td>a.button.tiny:contains("Slett")')->count());
        $this->assertEquals(2, $crawler->filter('td>a.button.tiny:contains("Les intervju")')->count());
    }

    public function testInterviewedAsAdmin()
    {
        $crawler = $this->adminGoTo('/kontrollpanel/opptakadmin?status=interviewed');
        
        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());
        $this->assertEquals(3, $crawler->filter('a:contains("Avdeling")')->count());
        $this->assertEquals(3, $crawler->filter('a:contains("Semester")')->count());
        $this->assertEquals(2, $crawler->filter('td>a.button:contains("Les intervju")')->count());
        $this->assertEquals(2, $crawler->filter('td>a.button:contains("Slett")')->count());
    }
    
    public function testAssistantIsDenied()
    {
        $client = self::createAssistantClient();

        $client->request('GET', '/kontrollpanel/opptakadmin');

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testShowApplicationsByDepartment()
    {

        // Superadmin user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'superadmin',
            'PHP_AUTH_PW' => '1234',
        ));

        // New applications
        $crawler = $client->request('GET', '/kontrollpanel/opptakadmin/avdeling/1?status=new');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());

        // Assert that we have the correct buttons for superadmin
        $this->assertEquals(1, $crawler->filter('a.button:contains("NTNU")')->count());
        $this->assertEquals(3, $crawler->filter('td>a:contains("Slett")')->count());
        $this->assertEquals(3, $crawler->filter('td>a:contains("Fordel")')->count());

        // Assigned to interview applications
        $crawler = $client->request('GET', '/kontrollpanel/opptakadmin/avdeling/1?status=assigned');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak NTNU")')->count());

        // Assert that we have the correct buttons for superadmin
        $this->assertEquals(1, $crawler->filter('a.button:contains("NTNU")')->count());
        $this->assertEquals(2, $crawler->filter('td>a:contains("Sett opp")')->count());
        $this->assertEquals(2, $crawler->filter('td>a:contains("Intervju")')->count());

        // Interviewed applications
        $crawler = $client->request('GET', '/kontrollpanel/opptakadmin/avdeling/1?status=interviewed');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak NTNU")')->count());

        // Assert that we have the correct buttons for superadmin
        $this->assertEquals(1, $crawler->filter('a.button:contains("NTNU")')->count());
        $this->assertEquals(2, $crawler->filter('td>a:contains("Les intervju")')->count());
        $this->assertEquals(2, $crawler->filter('td>a:contains("Slett")')->count());

        // Admin tests
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => '1234',
        ));

        // New applications
        $crawler = $client->request('GET', '/kontrollpanel/opptakadmin/avdeling/1?status=new');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());

        // Assert that we only have the buttons for admin
        $this->assertEquals(1, $crawler->filter('a.button:contains("NTNU")')->count());
        $this->assertEquals(3, $crawler->filter('td>a:contains("Fordel")')->count());

        // Assigned to interview applications
        $crawler = $client->request('GET', '/kontrollpanel/opptakadmin/avdeling/1?status=assigned');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());

        // Assert that we only have the buttons for admin
        $this->assertEquals(1, $crawler->filter('a.button:contains("NTNU")')->count());
        $this->assertEquals(2, $crawler->filter('td>a:contains("Sett opp")')->count());
        $this->assertEquals(2, $crawler->filter('td>a:contains("Intervju")')->count());

        // Interviewed applications
        $crawler = $client->request('GET', '/kontrollpanel/opptakadmin/avdeling/1?status=interviewed');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak NTNU")')->count());

        // Assert that we only have the buttons for admin
        $this->assertEquals(1, $crawler->filter('a.button:contains("NTNU")')->count());
        $this->assertEquals(0, $crawler->filter('td>a:contains("Slett")')->count());
        $this->assertEquals(2, $crawler->filter('td>a:contains("Les intervju")')->count());

        // Team user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'team',
            'PHP_AUTH_PW' => '1234',
        ));

        $client->request('GET', '/kontrollpanel/opptakadmin/avdeling/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('GET', '/kontrollpanel/opptakadmin/avdeling/2');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testCancelInterview()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/opptakadmin?status=assigned');
        $this->assertEquals(1, $crawler->filter('td:contains("Ruben")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Ravnå")')->count());

        $crawler = $client->request('GET', '/kontrollpanel/opptakadmin?status=new');
        $this->assertEquals(0, $crawler->filter('td:contains("Ruben")')->count());
        $this->assertEquals(0, $crawler->filter('td:contains("Ravnå")')->count());
    }
}
