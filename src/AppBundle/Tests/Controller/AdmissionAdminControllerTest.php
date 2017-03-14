<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdmissionAdminControllerTest extends WebTestCase
{
    public function testShow()
    {

        // Superadmin user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'superadmin',
            'PHP_AUTH_PW' => '1234',
        ));

        // New applications
        $crawler = $client->request('GET', '/kontrollpanel/opptak');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());

        // Assert that we have the correct buttons for superadmin
        $this->assertEquals(3, $crawler->filter('a:contains("Avdeling")')->count());
        $this->assertEquals(3, $crawler->filter('a:contains("Semester")')->count());
        $this->assertEquals(2, $crawler->filter('td>a:contains("Slett")')->count());
        $this->assertEquals(2, $crawler->filter('td>a:contains("Fordel")')->count());

        // Assigned to interview applications
        $crawler = $client->request('GET', '/kontrollpanel/opptak/fordelt');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());

        // Assert that we have the correct buttons for superadmin
        $this->assertEquals(3, $crawler->filter('a:contains("Avdeling")')->count());
        $this->assertEquals(3, $crawler->filter('a:contains("Semester")')->count());
        $this->assertEquals(2, $crawler->filter('td>a:contains("Sett opp")')->count());
        $this->assertEquals(2, $crawler->filter('td>a:contains("Intervju")')->count());

        // Interviewed applications
        $crawler = $client->request('GET', '/kontrollpanel/opptak/intervjuet');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());

        // Assert that we have the correct buttons for superadmin
        $this->assertEquals(3, $crawler->filter('a:contains("Avdeling")')->count());
        $this->assertEquals(3, $crawler->filter('a:contains("Semester")')->count());
        $this->assertEquals(2, $crawler->filter('td>a.button:contains("Les intervju")')->count());
        $this->assertEquals(2, $crawler->filter('td>a.button:contains("Slett")')->count());

        // Admin user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => '1234',
        ));

        // New applications
        $crawler = $client->request('GET', '/kontrollpanel/opptak');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());

        // Assert that we only have the buttons for admin
        $this->assertEquals(3, $crawler->filter('a:contains("Avdeling")')->count());
        $this->assertEquals(3, $crawler->filter('a:contains("Semester")')->count());
        $this->assertEquals(0, $crawler->filter('td>a:contains("Slett")')->count());
        $this->assertEquals(3, $crawler->filter('a:contains("Fordel")')->count());

        // Assigned to interview applications
        $crawler = $client->request('GET', '/kontrollpanel/opptak/fordelt');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());

        // Assert that we only have the buttons for admin
        $this->assertEquals(3, $crawler->filter('a:contains("Avdeling")')->count());
        $this->assertEquals(3, $crawler->filter('a:contains("Semester")')->count());
        $this->assertEquals(2, $crawler->filter('td>a:contains("Sett opp")')->count());
        $this->assertEquals(2, $crawler->filter('td>a:contains("Intervju")')->count());

        // Interviewed applications
        $crawler = $client->request('GET', '/kontrollpanel/opptak/intervjuet');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());

        // Assert that we only have the buttons for admin
        $this->assertEquals(3, $crawler->filter('a:contains("Avdeling")')->count());
        $this->assertEquals(3, $crawler->filter('a:contains("Semester")')->count());
        $this->assertEquals(0, $crawler->filter('td>a.button.tiny:contains("Slett")')->count());
        $this->assertEquals(2, $crawler->filter('td>a.button.tiny:contains("Les intervju")')->count());

        // Team user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'team',
            'PHP_AUTH_PW' => '1234',
        ));

        // New applications
        $crawler = $client->request('GET', '/kontrollpanel/opptak');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());

        // Assert that we only have the buttons for team
        $this->assertEquals(0, $crawler->filter('a.button:contains("Ny søker")')->count());
        $this->assertEquals(0, $crawler->filter('td>a.button:contains("Slett")')->count());
        $this->assertEquals(0, $crawler->filter('td>a.button:contains("Fordel")')->count());

        // Assign to interview applications
        $crawler = $client->request('GET', '/kontrollpanel/opptak/fordelt');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());

        // Assert that we only have the buttons for team
        $this->assertEquals(0, $crawler->filter('td>a:contains("Sett opp")')->count());
        $this->assertEquals(0, $crawler->filter('td>a:contains("Intervju")')->count());

        // Interviewed applications
        $crawler = $client->request('GET', '/kontrollpanel/opptak/intervjuet');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());

        // Assert that we only have the buttons for team
        $this->assertEquals(0, $crawler->filter('td>a:contains("Les intervju")')->count());
        $this->assertEquals(0, $crawler->filter('td>a:contains("Slett")')->count());

        // Assistant user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'assistent',
            'PHP_AUTH_PW' => '1234',
        ));

        $client->request('GET', '/kontrollpanel/opptak');

        // Assert that the page response status code is 403 Access denied
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
        $crawler = $client->request('GET', '/kontrollpanel/opptak/nye/2');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());

        // Assert that we have the correct buttons for superadmin
        $this->assertEquals(1, $crawler->filter('a.button:contains("NTNU")')->count());
        $this->assertEquals(2, $crawler->filter('td>a:contains("Slett")')->count());
        $this->assertEquals(2, $crawler->filter('td>a:contains("Fordel")')->count());

        // Assigned to interview applications
        $crawler = $client->request('GET', '/kontrollpanel/opptak/fordelt/2');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak NTNU")')->count());

        // Assert that we have the correct buttons for superadmin
        $this->assertEquals(1, $crawler->filter('a.button:contains("NTNU")')->count());
        $this->assertEquals(2, $crawler->filter('td>a:contains("Sett opp")')->count());
        $this->assertEquals(2, $crawler->filter('td>a:contains("Intervju")')->count());

        // Interviewed applications
        $crawler = $client->request('GET', '/kontrollpanel/opptak/intervjuet/2');

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
        $crawler = $client->request('GET', '/kontrollpanel/opptak/nye/2');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());

        // Assert that we only have the buttons for admin
        $this->assertEquals(1, $crawler->filter('a.button:contains("NTNU")')->count());
        $this->assertEquals(2, $crawler->filter('td>a:contains("Fordel")')->count());

        // Assigned to interview applications
        $crawler = $client->request('GET', '/kontrollpanel/opptak/fordelt/2');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());

        // Assert that we only have the buttons for admin
        $this->assertEquals(1, $crawler->filter('a.button:contains("NTNU")')->count());
        $this->assertEquals(2, $crawler->filter('td>a:contains("Sett opp")')->count());
        $this->assertEquals(2, $crawler->filter('td>a:contains("Intervju")')->count());

        // Interviewed applications
        $crawler = $client->request('GET', '/kontrollpanel/opptak/intervjuet/2');

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

        $client->request('GET', '/kontrollpanel/opptak/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('GET', '/kontrollpanel/opptak/4');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testCancelInterview()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/opptak/fordelt');
        $this->assertEquals(1, $crawler->filter('td:contains("Ruben")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Ravnå")')->count());

        $crawler = $client->request('GET', '/kontrollpanel/opptak/nye');
        $this->assertEquals(0, $crawler->filter('td:contains("Ruben")')->count());
        $this->assertEquals(0, $crawler->filter('td:contains("Ravnå")')->count());
    }

    /*
    Requires JQuery interaction, Symfony2 does not support that

    Phpunit was designed to test the PHP language, have to use another tool to test these.

    public function testDeleteApplicationById() {}
    public function bulkDeleteApplication() {}
    public function createUnactivatedUser() {}

    */

    /*
    The people that made the methods below must write their own functional tests
    Missing:

    public function createNewUser() {}

    */
}
