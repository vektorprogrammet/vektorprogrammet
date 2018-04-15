<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;

class SemesterControllerTest extends BaseWebTestCase
{
    public function testShowSemestersByDepartment()
    {

        // ADMIN
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'petjo',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/semesteradmin/avdeling/1');

        // Assert that we have the correct amount of data
        $this->assertEquals(1, $crawler->filter('h1:contains("Opptaksperioder NTNU")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Vår 2013")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Høst 2015")')->count());

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $crawler = $client->request('GET', '/kontrollpanel/semesteradmin/avdeling/2');

        // Assert that we have the correct amount of data
        $this->assertEquals(1, $crawler->filter('h1:contains("Opptaksperioder HiST")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Vår 2015")')->count());

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testSuperadminCreateSemester()
    {

        // ADMIN
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'petjo',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/semesteradmin/avdeling/opprett/1');

        // Assert that we have the correct amount of data
        $this->assertEquals(1, $crawler->filter('h1:contains("Opprett semester")')->count());

        $form = $crawler->selectButton('Opprett')->form();

        // Change the value of a field
        $form['createSemester[semesterTime]']->select('Vår');
        $form['createSemester[year]']->select('2017');
        $form['createSemester[admissionStartDate]'] = '2017-01-04 10:30:00 ';
        $form['createSemester[admissionEndDate]'] = '2017-02-02 10:30:00 ';

        // submit the form
        $client->submit($form);

        // Assert a specific 302 status code
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        // Assert that the response is the correct redirect
        $this->assertTrue($client->getResponse()->isRedirect('/kontrollpanel/semesteradmin/avdeling/1'));
    }

    public function testUpdateSemester()
    {

        // ADMIN
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'petjo',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/semesteradmin/avdeling/1');

        $this->assertEquals(4, $crawler->filter('a:contains("Rediger")')->count());

        // Find a link and click it
        $link = $crawler->selectLink('Rediger')->eq(1)->link();
        $crawler = $client->click($link);

        // Assert that we have the correct amount of data
        $this->assertEquals(1, $crawler->filter('h1:contains("Endre opptaksperiode")')->count());

        $form = $crawler->selectButton('Endre')->form();

        // Change the value of a field
        $form['createSemester[admissionStartDate]'] = '04.08.2015 10:30 ';
        $form['createSemester[admissionEndDate]'] = '02.09.2015 10:40 ';

        // submit the form
        $client->submit($form);

        // Assert a specific 302 status code
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        // Assert that the response is the correct redirect
        $this->assertTrue($client->getResponse()->isRedirect('/kontrollpanel/semesteradmin/avdeling/1'));

        // Follow the redirect
        $crawler = $client->followRedirect();

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals(1, $crawler->filter('h1:contains("Opptaksperioder NTNU")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("2015-08-04")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("10:30:00")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("2015-09-02")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("10:40:00")')->count());
    }

    public function testShow()
    {

        // TEAM
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'team',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/semesteradmin');

        // Assert that we have the correct amount of data
        $this->assertEquals(1, $crawler->filter('h1:contains("Opptaksperioder NTNU")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Vår 2013")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Høst 2015")')->count());

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // USER
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'assistent',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/skoleadmin/avdeling/2');

        // Assert that the response is a redirect to /
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    /*
    Requires JQuery interaction, Symfony2 does not support that

    Phpunit was designed to test the PHP language, have to use another tool to test these.

    public function testDeleteSemesterById() {}

    */
}
