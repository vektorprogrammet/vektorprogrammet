<?php

namespace Tests\AppBundle\Controller;

use Tests\BaseWebTestCase;

class InterviewControllerTest extends BaseWebTestCase
{
    /**
     * @param bool $teamInterest
     * @param $client
     * @param $crawler
     */
    private function fillAndSubmitInterviewFormWithTeamInterest($client, $crawler, bool $teamInterest)
    {
        // Find the form
        $form = $crawler->selectButton('Lagre og send kvittering')->form();

        // Fill in the form
        $form['application[applicationPractical][days][monday]']->tick();
        $form['application[applicationPractical][days][tuesday]']->tick();
        $form['application[applicationPractical][days][wednesday]']->untick();
        $form['application[applicationPractical][days][thursday]']->tick();
        $form['application[applicationPractical][days][friday]']->untick();

        $form['application[applicationPractical][doublePosition]']->select('1');
        $form['application[applicationPractical][preferredGroup]']->select('Bolk 1');
        $form['application[applicationPractical][language]']->select('Norsk');

        $form['application[interview][interviewAnswers][0][answer]'] = 'Test answer';
        $form['application[interview][interviewAnswers][1][answer]'] = 'Test answer';
        $form['application[applicationPractical][teamInterest]'] = $teamInterest;

        $form['application[interview][interviewScore][explanatoryPower]']->select(5);
        $form['application[interview][interviewScore][roleModel]']->select(4);
        $form['application[interview][interviewScore][suitability]']->select(6);

        $form['application[interview][interviewScore][suitableAssistant]']->select('Ja');

        if ($teamInterest) {
            $form['application[applicationPractical][potentialTeams]'][0]->tick();
        }

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
        $form = $crawler->selectButton('Lagre og send kvittering')->form();

        // Fill in the form
        $form['application[applicationPractical][days][monday]']->tick();
        $form['application[applicationPractical][days][tuesday]']->tick();
        $form['application[applicationPractical][days][wednesday]']->untick();
        $form['application[applicationPractical][days][thursday]']->tick();
        $form['application[applicationPractical][days][friday]']->untick();

        $form['application[applicationPractical][doublePosition]']->select('1');
        $form['application[applicationPractical][preferredGroup]']->select('Bolk 1');
        $form['application[applicationPractical][language]']->select('Norsk og engelsk');

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
        $this->assertEquals(2, $crawler->filter('td:contains("Bra")')->count());
        $this->assertEquals(3, $crawler->filter('td:contains("Ikke")')->count());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("5")')->count());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("4")')->count());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("6")')->count());
    }

    private function verifyCorrectInterview($crawler, $firstName, $lastName)
    {
        $this->assertEquals(1, $crawler->filter('td:contains("'.$firstName.' '.$lastName.'")')->count());
    }

    public function testConduct()
    {
        // Admin user
        $client = $this->createTeamLeaderClient();

        $crawler = $this->goTo('/kontrollpanel/intervju/conduct/6', $client);

        // Assert that we have the correct page
        $this->verifyCorrectInterview($crawler, 'Assistent', 'Johansen');

        $this->fillAndSubmitInterviewForm($client, $crawler);

        $crawler = $client->followRedirect();

        // Assert that we have the correct page with the correct info (from the submitted form)
        $this->assertEquals(1, $crawler->filter('h2:contains("Opptak")')->count());
        $this->assertGreaterThanOrEqual(1, $crawler->filter('td:contains("Assistent Johansen")')->count());

        $crawler = $client->request('GET', '/kontrollpanel/intervju/vis/6');
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
        $this->assertEquals(1, $crawler->filter('h2:contains("Opptak")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Erik Trondsen")')->count());

        $crawler = $client->request('GET', '/kontrollpanel/intervju/vis/5');
        $this->verifyInterview($crawler);
    }

    public function testShow()
    {
        $client = $this->createTeamLeaderClient();

        $crawler = $client->request('GET', '/kontrollpanel/intervju/vis/4');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->verifyCorrectInterview($crawler, 'Markus', 'Gundersen');
        $this->assertEquals(2, $crawler->filter('p:contains("Test answer")')->count());
    }

    public function testCreateSchema()
    {
        // Admin user
        $client = $this->createTeamLeaderClient();

        $crawler = $client->request('GET', '/kontrollpanel/intervju/skjema/opprett');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Find the form
        $form = $crawler->selectButton('Lagre')->form();

        // Fill in the form
        $form['interviewSchema[name]'] = 'Test skjema1';

        // Submit the form
        $client->submit($form);

        // Follow the redirect
        $crawler = $client->followRedirect();

        // Assert that we have the correct page with the correct info (from the submitted form)
        $this->assertGreaterThanOrEqual(1, $crawler->filter('td:contains("Test skjema1")')->count());
    }

    public function testEditSchemas()
    {
        // Admin user
        $client = $this->createTeamLeaderClient();

        $crawler = $client->request('GET', '/kontrollpanel/intervju/skjema/1');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Find the form
        $form = $crawler->selectButton('Lagre')->form();

        // Fill in the form
        $form['interviewSchema[name]'] = 'Intervjuskjema HiST oppdatert, 2015';

        // Submit the form
        $client->submit($form);

        // Follow the redirect
        $crawler = $client->followRedirect();

        // Assert that we have the correct page with the correct info (from the submitted form)
        $this->assertGreaterThanOrEqual(1, $crawler->filter('td:contains("Intervjuskjema HiST oppdatert, 2015")')->count());
    }

    public function testShowSchemas()
    {
        // Admin user
        $client = $this->createTeamLeaderClient();

        $crawler = $client->request('GET', '/kontrollpanel/intervju/skjema');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertGreaterThanOrEqual(1, $crawler->filter('td:contains("Intervjuskjema NTNU, 2015")')->count());
    }

    public function testSchedule()
    {
        // Admin user
        $client = $this->createTeamLeaderClient();

        $crawler = $client->request('GET', '/kontrollpanel/intervju/settopp/6');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h2:contains("Intervjuoppsett")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Assistent Johansen")')->count());

        // Find the form
        $form = $crawler->selectButton('Send invitasjon på sms og e-post')->form();

        // Fill in the form
        $form['scheduleInterview[datetime]'] = '10.08.2015 15:00';

        // Submit the form
        $client->submit($form);

        // Follow the redirect
        $crawler = $client->followRedirect();

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h2:contains("Opptak")')->count());

        // Team user who is assigned the interview
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'idaan',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/intervju/settopp/6');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h2:contains("Intervjuoppsett")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Assistent Johansen")')->count());

        // Find the form
        $form = $crawler->selectButton('Send invitasjon på sms og e-post')->form();

        // Fill in the form
        $form['scheduleInterview[datetime]'] = '10.08.2015 15:00';

        // Submit the form
        $client->submit($form);

        // Follow the redirect
        $crawler = $client->followRedirect();

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h2:contains("Opptak")')->count());
    }

    public function testWantTeamInterest()
    {
        $rowsBefore = $this->countTableRows('/kontrollpanel/opptakadmin/teaminteresse?department=1&semester=1');

        // Admin user

        $crawler = $this->adminGoTo('/kontrollpanel/intervju/conduct/6');

        // Assert that we have the correct page
        $this->verifyCorrectInterview($crawler, 'Assistent', 'Johansen');

        $this->fillAndSubmitInterviewFormWithTeamInterest(self::createAdminClient(), $crawler, true);

        $rowsAfter = $this->countTableRows('/kontrollpanel/opptakadmin/teaminteresse?department=1&semester=1');
        $this->assertEquals($rowsBefore + 2, $rowsAfter); // One new row in each table
    }

    public function testNotWantTeamInterest()
    {
        $rowsBefore = $this->countTableRows('/kontrollpanel/opptakadmin/teaminteresse?department=1&semester=1');

        // Admin user

        $crawler = $this->adminGoTo('/kontrollpanel/intervju/conduct/6');

        // Assert that we have the correct page
        $this->verifyCorrectInterview($crawler, 'Assistent', 'Johansen');

        $this->fillAndSubmitInterviewFormWithTeamInterest(self::createAdminClient(), $crawler, false);

        $rowsAfter = $this->countTableRows('/kontrollpanel/opptakadmin/teaminteresse?department=1&semester=1');

        $this->assertEquals($rowsBefore, $rowsAfter);
    }

    public function testUpdateStatus()
    {
        $client = $this->createAdminClient();
        $crawler = $this->goTo('/kontrollpanel/opptak/fordelt?department=1&semester=1', $client);
        $before = $crawler->filter('td:contains("Ikke satt opp")')->count();

        $crawler = $this->goTo("/kontrollpanel/intervju/settopp/6", $client);
        $saveButton = $crawler->filter('div#statusModal button:contains("Lagre")');
        $this->assertNotNull($saveButton);
        $form = $saveButton->form();
        $this->assertNotNull($form);
        $form['status'] = 1;
        $client->submit($form);
        $client->followRedirect();

        $crawler = $this->goTo('/kontrollpanel/opptak/fordelt?department=1&semester=1', $client);
        $after = $crawler->filter('td:contains("Ikke satt opp")')->count();
        $this->assertEquals($before - 1, $after);
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
