<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;

class InterviewControllerTest extends BaseWebTestCase
{
    /**
     * @param bool teamInterest
     * @param $client
     * @param $crawler
     */
    private function fillAndSubmitInterviewFormWithTeamInterest($client, $crawler, bool $teamInterest)
    {
        // Find the form
        $form = $crawler->selectButton('Lagre')->form();

        // Fill in the form
        $form['application[applicationPractical][days][monday]']->select('Bra');
        $form['application[applicationPractical][days][tuesday]']->select('Bra');
        $form['application[applicationPractical][days][wednesday]']->select('Ikke');
        $form['application[applicationPractical][days][thursday]']->select('Bra');
        $form['application[applicationPractical][days][friday]']->select('Ikke');

        $form['application[applicationPractical][doublePosition]']->select('1');
        $form['application[applicationPractical][preferredGroup]']->select('Bolk 1');
        $form['application[applicationPractical][english]']->select('1');

        $form['application[interview][interviewAnswers][0][answer]'] = 'Test answer';
        $form['application[interview][interviewAnswers][1][answer]'] = 'Test answer';
        $form['application[applicationPractical][teamInterest]'] = $teamInterest;

        $form['application[interview][interviewScore][explanatoryPower]']->select(5);
        $form['application[interview][interviewScore][roleModel]']->select(4);
        $form['application[interview][interviewScore][suitability]']->select(6);

        $form['application[interview][interviewScore][suitableAssistant]']->select('Ja');

        // Submit the form
        $client->submit($form);
    }
    /**
     * @param $client
     * @param $crawler
     */
    private function fillAndSubmitInterviewForm($client, $crawler)
    {
        // Find the form
        $form = $crawler->selectButton('Lagre')->form();

        // Fill in the form
        $form['application[applicationPractical][days][monday]']->select('Bra');
        $form['application[applicationPractical][days][tuesday]']->select('Bra');
        $form['application[applicationPractical][days][wednesday]']->select('Ikke');
        $form['application[applicationPractical][days][thursday]']->select('Bra');
        $form['application[applicationPractical][days][friday]']->select('Ikke');

        $form['application[applicationPractical][doublePosition]']->select('1');
        $form['application[applicationPractical][preferredGroup]']->select('Bolk 1');
        $form['application[applicationPractical][english]']->select('1');

        $form['application[interview][interviewAnswers][0][answer]'] = 'Test answer';
        $form['application[interview][interviewAnswers][1][answer]'] = 'Test answer';

        $form['application[interview][interviewScore][explanatoryPower]']->select(5);
        $form['application[interview][interviewScore][roleModel]']->select(4);
        $form['application[interview][interviewScore][suitability]']->select(6);

        $form['application[interview][interviewScore][suitableAssistant]']->select('Ja');

        // Submit the form
        $client->submit($form);
    }

    private function verifyInterview($crawler)
    {
        $this->assertEquals(2, $crawler->filter('p:contains("Test answer")')->count());
        $this->assertEquals(3, $crawler->filter('td:contains("Bra")')->count());
        $this->assertEquals(2, $crawler->filter('td:contains("Ikke")')->count());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("5")')->count());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("4")')->count());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("6")')->count());
    }

    private function verifyCorrectInterview($crawler, $firstName, $lastName)
    {
        $this->assertEquals(1, $crawler->filter('h1:contains("Intervju")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("'.$firstName.'")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("'.$lastName.'")')->count());
    }

    public function testConduct()
    {
        // Admin user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/intervju/conduct/5');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->verifyCorrectInterview($crawler, 'Erik', 'Trondsen');

        $this->fillAndSubmitInterviewForm($client, $crawler);

        // Follow the redirect
        $crawler = $client->followRedirect();

        // Assert that we have the correct page with the correct info (from the submitted form)
        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Erik")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Trondsen")')->count());

        $crawler = $client->request('GET', '/kontrollpanel/intervju/vis/5');
        $this->verifyInterview($crawler);


        // Team user who is assigned the interview
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'idaan',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/intervju/conduct/5');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->verifyCorrectInterview($crawler, 'Erik', 'Trondsen');

        $this->fillAndSubmitInterviewForm($client, $crawler);

        // Follow the redirect
        $crawler = $client->followRedirect();

        // Assert that we have the correct page with the correct info (from the submitted form)
        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Erik")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Trondsen")')->count());

        $crawler = $client->request('GET', '/kontrollpanel/intervju/vis/5');
        $this->verifyInterview($crawler);

        // Team user who is not assigned the interview
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'team',
            'PHP_AUTH_PW' => '1234',
        ));

        $client->request('GET', '/kontrollpanel/intervju/conduct/6');

        // Assert that the page response status code is 403 access denied
        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        // Assistant user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'assistent',
            'PHP_AUTH_PW' => '1234',
        ));

        $client->request('GET', '/kontrollpanel/intervju/conduct/6');

        // Assert that the page response status code is 403 access denied
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testShow()
    {
        // Admin user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/intervju/vis/4');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->verifyCorrectInterview($crawler, 'Markus', 'Gundersen');
        $this->assertEquals(2, $crawler->filter('p:contains("Test answer")')->count());

        // Team user from the same department as the applicant
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'team',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/intervju/vis/4');

        // Assert that the page response status code is 200
        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        // Team user from a different department than the applicant
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'kribo',
            'PHP_AUTH_PW' => '1234',
        ));

        $client->request('GET', '/kontrollpanel/intervju/vis/4');

        // Assert that the page response status code is 403 access denied
        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        // Assistant user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'assistent',
            'PHP_AUTH_PW' => '1234',
        ));

        $client->request('GET', '/kontrollpanel/intervju/vis/4');

        // Assert that the page response status code is 403 access denied
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testCreateSchema()
    {
        // Admin user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/intervju/skjema/opprett');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Intervjuskjema")')->count());

        // Find the form
        $form = $crawler->selectButton('Lagre')->form();

        // Fill in the form
        $form['interviewSchema[name]'] = 'Test skjema1';

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
            'PHP_AUTH_PW' => '1234',
        ));

        $client->request('GET', '/kontrollpanel/intervju/skjema/opprett');

        // Assert that the page response status code is 403 access denied
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testEditSchemas()
    {
        // Admin user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/intervju/skjema/1');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Intervjuskjema")')->count());

        // Find the form
        $form = $crawler->selectButton('Lagre')->form();

        // Fill in the form
        $form['interviewSchema[name]'] = 'Intervjuskjema HiST oppdatert, 2015';

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
            'PHP_AUTH_PW' => '1234',
        ));

        $client->request('GET', '/kontrollpanel/intervju/skjema/1');

        // Assert that the page response status code is 403 access denied
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testShowSchemas()
    {
        // Admin user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/intervju/skjema');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Intervjuskjemaer")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Intervjuskjema NTNU, 2015")')->count());

        // Team user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'team',
            'PHP_AUTH_PW' => '1234',
        ));

        $client->request('GET', '/kontrollpanel/intervju/skjema');

        // Assert that the page response status code is 403 access denied
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testSchedule()
    {
        // Admin user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/intervju/settopp/6');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h3:contains("Sett opp intervju")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Assistent")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Johansen")')->count());

        // Find the form
        $form = $crawler->selectButton('Lagre tidspunkt')->form();

        // Fill in the form
        $form['scheduleInterview[datetime]'] = '2015-08-10 15:00:00';

        // Submit the form
        $client->submit($form);

        // Follow the redirect
        $crawler = $client->followRedirect();

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());

        // Team user who is assigned the interview
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'idaan',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/intervju/settopp/6');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h3:contains("Sett opp intervju")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Assistent")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Johansen")')->count());

        // Find the form
        $form = $crawler->selectButton('Lagre tidspunkt')->form();

        // Fill in the form
        $form['scheduleInterview[datetime]'] = '2015-08-10 15:00:00';

        // Submit the form
        $client->submit($form);

        // Follow the redirect
        $crawler = $client->followRedirect();

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());

        // Team user who is not assigned the interview
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'team',
            'PHP_AUTH_PW' => '1234',
        ));

        $client->request('GET', '/kontrollpanel/intervju/settopp/6');

        // Assert that the page response status code is 403 access denied
        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        // Assistant user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'assistent',
            'PHP_AUTH_PW' => '1234',
        ));

        $client->request('GET', '/kontrollpanel/intervju/settopp/6');

        // Assert that the page response status code is 403 access denied
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testWantTeamInterest()
    {
        $applicationsBefore = $this->countTableRows('/kontrollpanel/opptakadmin/teaminteresse/2');

        // Admin user

        $crawler = $this->adminGoTo('/kontrollpanel/intervju/conduct/6');

        // Assert that we have the correct page
        $this->verifyCorrectInterview($crawler, 'Assistent', 'Johansen');

        $this->fillAndSubmitInterviewFormWithTeamInterest(self::createAdminClient(), $crawler, true);

        $applicationsAfter = $this->countTableRows('/kontrollpanel/opptakadmin/teaminteresse/2');

        $this->assertEquals($applicationsBefore + 1, $applicationsAfter);
    }

    public function testNotWantTeamInterest()
    {
        $applicationsBefore = $this->countTableRows('/kontrollpanel/opptakadmin/teaminteresse/2');

        // Admin user

        $crawler = $this->adminGoTo('/kontrollpanel/intervju/conduct/6');

        // Assert that we have the correct page
        $this->verifyCorrectInterview($crawler, 'Assistent', 'Johansen');

        $this->fillAndSubmitInterviewFormWithTeamInterest(self::createAdminClient(), $crawler, false);

        $applicationsAfter = $this->countTableRows('/kontrollpanel/opptakadmin/teaminteresse/2');

        $this->assertEquals($applicationsBefore, $applicationsAfter);
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
