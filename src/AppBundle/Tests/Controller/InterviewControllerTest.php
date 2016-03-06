<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class InterviewControllerTest extends WebTestCase
{

    /**
     * @param $client
     * @param $crawler
     */
    private function fillAndSubmitInterviewForm($client, $crawler){
        // Find the form
        $form = $crawler->selectButton('Lagre')->form();

        // Fill in the form
        $form['interview[interviewPractical][monday]']->select("Bra");
        $form['interview[interviewPractical][tuesday]']->select("Bra");
        $form['interview[interviewPractical][wednesday]']->select("Ikke");
        $form['interview[interviewPractical][thursday]']->select("Bra");
        $form['interview[interviewPractical][friday]']->select("Ikke");

        $form['interview[interviewPractical][doublePosition]']->select("1");
        $form['interview[interviewPractical][preferredGroup]']->select("Bolk 1");
        $form['interview[interviewPractical][english]']->select("1");

        $form['interview[interviewAnswers][0][answer]'] = "Test answer";
        $form['interview[interviewAnswers][1][answer]'] = "Test answer";

        $form['interview[interviewScore][explanatoryPower]']->select(5);
        $form['interview[interviewScore][roleModel]']->select(4);
        $form['interview[interviewScore][suitability]']->select(6);

        $form['interview[interviewScore][suitableAssistant]']->select("Ja");

        // Submit the form
        $client->submit($form);
    }

    private function verifyInterview($crawler){
        $this->assertEquals(2, $crawler->filter('p:contains("Test answer")')->count());
        $this->assertEquals(3, $crawler->filter('td:contains("Bra")')->count());
        $this->assertEquals(2, $crawler->filter('td:contains("Ikke")')->count());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("5")')->count());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("4")')->count());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("6")')->count());
    }

    public function testConduct()
    {
        // Admin user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => '1234',
        ));

        $crawler = $client->request('GET', '/intervju/conduct/3');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Intervju")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Mark")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Zuckerberg")')->count());

        $this->fillAndSubmitInterviewForm($client, $crawler);

        // Follow the redirect
        $crawler = $client->followRedirect();

        // Assert that we have the correct page with the correct info (from the submitted form)
        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Mark")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Zuckerberg")')->count());

        $crawler = $client->request('GET', '/intervju/vis/3');
        $this->verifyInterview($crawler);
        restoreDatabase();

        // Team user who is assigned the interview
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'idaan',
            'PHP_AUTH_PW'   => '1234',
        ));

        $crawler = $client->request('GET', '/intervju/conduct/3');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Intervju")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Mark")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Zuckerberg")')->count());

        $this->fillAndSubmitInterviewForm($client, $crawler);

        // Follow the redirect
        $crawler = $client->followRedirect();

        // Assert that we have the correct page with the correct info (from the submitted form)
        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Mark")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Zuckerberg")')->count());

        $crawler = $client->request('GET', '/intervju/vis/3');
        $this->verifyInterview($crawler);


        // Team user who is not assigned the interview
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'team',
            'PHP_AUTH_PW'   => '1234',
        ));

        $client->request('GET', '/intervju/conduct/3');

        // Assert that the page response status code is 403 access denied
        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        // Assistant user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'assistent',
            'PHP_AUTH_PW'   => '1234',
        ));

        $client->request('GET', '/intervju/conduct/3');

        // Assert that the page response status code is 403 access denied
        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        restoreDatabase();
    }

    public function testShow()
    {
        // Admin user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => '1234',
        ));

        $crawler = $client->request('GET', '/intervju/vis/2');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Intervju")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Walter")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("White")')->count());
        $this->assertEquals(2, $crawler->filter('p:contains("Test answer")')->count());

        // Team user from the same department as the applicant
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'team',
            'PHP_AUTH_PW'   => '1234',
        ));

        $crawler = $client->request('GET', '/intervju/vis/1');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Intervju")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Leonardo")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("DiCaprio")')->count());
        $this->assertEquals(2, $crawler->filter('p:contains("Test answer")')->count());

        //Try viewing an interview that the team user did not conduct
        $crawler = $client->request('GET', '/intervju/vis/2');

        // Assert that the page response status code is 403
        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        // Team user from a different department than the applicant
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'kribo',
            'PHP_AUTH_PW'   => '1234',
        ));

        $client->request('GET', '/intervju/vis/1');

        // Assert that the page response status code is 403 access denied
        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        // Assistant user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'assistent',
            'PHP_AUTH_PW'   => '1234',
        ));

        $client->request('GET', '/intervju/vis/1');

        // Assert that the page response status code is 403 access denied
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testCreateSchema()
    {
        // Admin user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => '1234',
        ));

        $crawler = $client->request('GET', '/intervju/skjema/opprett');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Intervjuskjema")')->count());

        // Find the form
        $form = $crawler->selectButton('Lagre')->form();

        // Fill in the form
        $form['interviewSchema[name]'] = "Test skjema1";

        // Submit the form
        $client->submit($form);

        // Follow the redirect
        $crawler = $client->followRedirect();

        // Assert that we have the correct page with the correct info (from the submitted form)
        $this->assertEquals(1, $crawler->filter('h1:contains("Intervjuskjemaer")')->count());
        $this->assertGreaterThanOrEqual(1, $crawler->filter('td:contains("Test skjema1")')->count());


        // Team user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'team',
            'PHP_AUTH_PW'   => '1234',
        ));

        $client->request('GET', '/intervju/skjema/opprett');

        // Assert that the page response status code is 403 access denied
        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        restoreDatabase();
    }

    public function testEditSchemas()
    {
        // Admin user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => '1234',
        ));

        $crawler = $client->request('GET', '/intervju/skjema/1');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Intervjuskjema")')->count());

        // Find the form
        $form = $crawler->selectButton('Lagre')->form();

        // Fill in the form
        $form['interviewSchema[name]'] = "Intervjuskjema HiST oppdatert, 2015";

        // Submit the form
        $client->submit($form);

        // Follow the redirect
        $crawler = $client->followRedirect();

        // Assert that we have the correct page with the correct info (from the submitted form)
        $this->assertEquals(1, $crawler->filter('h1:contains("Intervjuskjemaer")')->count());
        $this->assertGreaterThanOrEqual(1, $crawler->filter('td:contains("Intervjuskjema HiST oppdatert, 2015")')->count());


        // Team user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'team',
            'PHP_AUTH_PW'   => '1234',
        ));

        $client->request('GET', '/intervju/skjema/1');

        // Assert that the page response status code is 403 access denied
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
        restoreDatabase();
    }

    public function testShowSchemas()
    {
        // Admin user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => '1234',
        ));

        $crawler = $client->request('GET', '/intervju/skjema');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Intervjuskjemaer")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Intervjuskjema NTNU, 2015")')->count());


        // Team user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'team',
            'PHP_AUTH_PW'   => '1234',
        ));

        $client->request('GET', '/intervju/skjema');

        // Assert that the page response status code is 403 access denied
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }


    public function testSchedule()
    {
        // Admin user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => '1234',
        ));

        $crawler = $client->request('GET', '/intervju/settopp/3');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h3:contains("Sett opp intervju")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Mark")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Zuckerberg")')->count());

        // Find the form
        $form = $crawler->selectButton('Lagre tidspunkt')->form();

        // Fill in the form
        $form['scheduleInterview[datetime]'] = "2015-08-10 15:00:00";

        // Submit the form
        $client->submit($form);

        // Follow the redirect
        $crawler = $client->followRedirect();

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());


        // Team user who is assigned the interview
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'idaan',
            'PHP_AUTH_PW'   => '1234',
        ));

        $crawler = $client->request('GET', '/intervju/settopp/3');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h3:contains("Sett opp intervju")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Mark")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Zuckerberg")')->count());

        // Find the form
        $form = $crawler->selectButton('Lagre tidspunkt')->form();

        // Fill in the form
        $form['scheduleInterview[datetime]'] = "2015-08-10 15:00:00";

        // Submit the form
        $client->submit($form);

        // Follow the redirect
        $crawler = $client->followRedirect();

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());


        // Team user who is not assigned the interview
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'team',
            'PHP_AUTH_PW'   => '1234',
        ));

        $client->request('GET', '/intervju/settopp/3');

        // Assert that the page response status code is 403 access denied
        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        // Assistant user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'assistent',
            'PHP_AUTH_PW'   => '1234',
        ));

        $client->request('GET', '/intervju/settopp/3');

        // Assert that the page response status code is 403 access denied
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
        restoreDatabase();
    }

    /*
    Requires JQuery interaction, Symfony2 does not support that

    Phpunit was designed to test the PHP language, have to use another tool to test these.

    public function testDeleteInterview() {}
    public function testBulkDeleteInterview() {}
    public function testAssign() {}
    public function testBulkAssign() {}

    */
}