<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class AdmissionAdminControllerTest extends BaseWebTestCase
{
    public function testShowAsTeamMember()
    {
        $crawler = $this->teamMemberGoTo('/kontrollpanel/opptak');

        $this->assertEquals(1, $crawler->filter('h2:contains("Opptak")')->count());
        $this->assertEquals(0, $crawler->filter('a.btn:contains("Ny søker")')->count());
        $this->assertEquals(0, $crawler->filter('option:contains("Fordel intervju")')->count());
        $this->assertEquals(0, $crawler->filter('option:contains("Slett søknad")')->count());
        $this->assertEquals(0, $crawler->filter('a.btn:contains("Utfør")')->count());
    }

    public function testShowAsTeamLeader()
    {
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/opptak');

        $this->assertEquals(1, $crawler->filter('h2:contains("Opptak")')->count());
        $this->assertEquals(1, $crawler->filter('a.btn:contains("Ny søker")')->count());
        $this->assertEquals(1, $crawler->filter('option:contains("Fordel intervju")')->count());
        $this->assertEquals(0, $crawler->filter('option:contains("Slett søknad")')->count());
        $this->assertEquals(1, $crawler->filter('a.btn:contains("Utfør")')->count());
    }

    public function testShowAsAdmin()
    {
        $crawler = $this->adminGoTo('/kontrollpanel/opptak');

        $this->assertEquals(1, $crawler->filter('h2:contains("Opptak")')->count());
        $this->assertEquals(1, $crawler->filter('a.btn:contains("Ny søker")')->count());
        $this->assertEquals(1, $crawler->filter('option:contains("Fordel intervju")')->count());
        $this->assertEquals(1, $crawler->filter('option:contains("Slett søknad")')->count());
        $this->assertEquals(1, $crawler->filter('a.btn:contains("Utfør")')->count());
    }

    public function testAssignedAsTeamMember()
    {
        $crawler = $this->teamMemberGoTo('/kontrollpanel/opptak/fordelt');

        $this->assertEquals(1, $crawler->filter('h2:contains("Opptak")')->count());
        $this->assertEquals(0, $crawler->filter('td a:contains("Sett opp")')->count());
        $this->assertEquals(0, $crawler->filter('td a:contains("Start intervju")')->count());
    }

    public function testAssignedAsTeamLeader()
    {
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/opptak/fordelt');

        $this->assertEquals(1, $crawler->filter('h2:contains("Opptak")')->count());
        $this->assertGreaterThanOrEqual(1, $crawler->filter('td a:contains("Sett opp")')->count());
        $this->assertGreaterThanOrEqual(1, $crawler->filter('td a:contains("Start intervju")')->count());
    }

    public function testAssignedAsAdmin()
    {
        $crawler = $this->adminGoTo('/kontrollpanel/opptak/fordelt');

        $this->assertEquals(1, $crawler->filter('h2:contains("Opptak")')->count());
        $this->assertGreaterThanOrEqual(1, $crawler->filter('td a:contains("Sett opp")')->count());
        $this->assertGreaterThanOrEqual(1, $crawler->filter('td a:contains("Start intervju")')->count());
    }

    public function testInterviewedAsTeamMember()
    {
        $crawler = $this->teamMemberGoTo('/kontrollpanel/opptak/intervjuet');

        $this->assertEquals(1, $crawler->filter('h2:contains("Opptak")')->count());
        $this->assertEquals(0, $crawler->filter('td button:contains("Slett")')->count());
    }

    public function testInterviewedAsTeamLeader()
    {
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/opptak/intervjuet');

        $this->assertEquals(1, $crawler->filter('h2:contains("Opptak")')->count());
        $this->assertGreaterThanOrEqual(0, $crawler->filter('td button:contains("Slett")')->count());
    }

    public function testInterviewedAsAdmin()
    {
        $crawler = $this->adminGoTo('/kontrollpanel/opptak/intervjuet');

        $this->assertEquals(1, $crawler->filter('h2:contains("Opptak")')->count());
        $this->assertGreaterThanOrEqual(1, $crawler->filter('td button:contains("Slett")')->count());
    }

    public function testShowApplicationsByDepartment()
    {
        // Superadmin user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'superadmin',
            'PHP_AUTH_PW' => '1234',
        ));

        // New applications
        $crawler = $client->request('GET', '/kontrollpanel/opptak/nye?department=1&semester=1');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h2:contains("Opptak")')->count());

        // Assert that we have the correct buttons for superadmin
        $this->assertEquals(1, $crawler->filter('a.button:contains("Trondheim")')->count());
        $this->assertEquals(2, $crawler->filter('td>a:contains("Slett")')->count());
        $this->assertEquals(2, $crawler->filter('td>a:contains("Fordel")')->count());

        // Assigned to interview applications
        $crawler = $client->request('GET', '/kontrollpanel/opptak/fordelt?department=1&semester=1');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak Trondheim")')->count());

        // Assert that we have the correct buttons for superadmin
        $this->assertEquals(1, $crawler->filter('a.button:contains("Trondheim")')->count());
        $this->assertEquals(2, $crawler->filter('td>a:contains("Sett opp")')->count());
        $this->assertEquals(2, $crawler->filter('td>a:contains("Intervju")')->count());

        // Interviewed applications
        $crawler = $client->request('GET', '/kontrollpanel/opptak/intervjuet?department=1&semester=1');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak Trondheim")')->count());

        // Assert that we have the correct buttons for superadmin
        $this->assertEquals(1, $crawler->filter('a.button:contains("Trondheim")')->count());
        $this->assertGreaterThan(1, $crawler->filter('td>a:contains("Les intervju")')->count());
        $this->assertGreaterThan(1, $crawler->filter('td>a:contains("Slett")')->count());

        // Admin tests
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => '1234',
        ));

        // New applications
        $crawler = $client->request('GET', '/kontrollpanel/opptak/nye?department=1&semester=1');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());

        // Assert that we only have the buttons for admin
        $this->assertEquals(1, $crawler->filter('a.button:contains("Trondheim")')->count());
        $this->assertEquals(2, $crawler->filter('td>a:contains("Fordel")')->count());

        // Assigned to interview applications
        $crawler = $client->request('GET', '/kontrollpanel/opptak/fordelt?department=1&semester=1');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());

        // Assert that we only have the buttons for admin
        $this->assertEquals(1, $crawler->filter('a.button:contains("Trondheim")')->count());
        $this->assertGreaterThan(1, $crawler->filter('td>a:contains("Sett opp")')->count());
        $this->assertGreaterThan(1, $crawler->filter('td>a:contains("Intervju")')->count());

        // Interviewed applications
        $crawler = $client->request('GET', '/kontrollpanel/opptak/intervjuet?department=1&semester=1');

        // Assert that the page response status code is 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak Trondheim")')->count());

        // Assert that we only have the buttons for admin
        $this->assertEquals(1, $crawler->filter('a.button:contains("Trondheim")')->count());
        $this->assertEquals(0, $crawler->filter('td>a:contains("Slett")')->count());
        $this->assertGreaterThan(1, $crawler->filter('td>a:contains("Les intervju")')->count());

        // Team user
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'team',
            'PHP_AUTH_PW' => '1234',
        ));

        $client->request('GET', '/kontrollpanel/opptak?department=1&semester=1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('GET', '/kontrollpanel/opptak?department=2&semester=1');
    }

    public function testCancelInterview()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/opptak/fordelt');
        $this->assertGreaterThanOrEqual(1, $crawler->filter('td:contains("Ruben Ravnå")')->count());

        $crawler = $client->request('GET', '/kontrollpanel/opptak/nye');
        $this->assertEquals(0, $crawler->filter('td:contains("Ruben Ravnå")')->count());
    }

    /**
     * Test the functions on /intervju/code.
     */
    public function testAcceptInterview()
    {
        // Test accept
        $this->helperTestStatus('Akseptert', 'Godta', 'Intervjuet ble akseptert.');
    }

    public function testNewTimeInterview()
    {
        $this->helperTestStatus('Ny tid ønskes', 'Be om ny tid', 'Forespørsel har blitt sendt.');
    }

    public function testUserCancelInterview()
    {
        $this->helperTestStatus('Kansellert', 'Kanseller', 'Intervjuet ble kansellert.');
    }

    /**
     * Test the status functionality on /intervju/code.
     *
     * Start at kontrollpanel/opptak/fordelt and count occurrences of $status and "Ingen
     * svar". Then, set up an interview and arrange for an email to be sent to the candidate.
     * Examine the contents of the email and extract the unique response code. Proceed to the
     * schedule response page with our special code and click the button corresponding to
     * $button_text. If this is a cancellation or a request for new time, we verify that an email
     * is sent to the interviewer. If this is a cancellation, we go through the cancel confirmation page.
     * Afterwards, verify that we get the correct flash message after the redirect. Finally,
     * go back to assigned page and check that the number of elements containing $status has
     * increased and that the number of elements containing "Ingen svar" har decreased.
     *
     * @param string $status
     * @param string $button_text
     * @param string $flash_text
     */
    private function helperTestStatus(string $status, string $button_text, string $flash_text)
    {
        $crawler = $this->teamMemberGoTo('/kontrollpanel/opptak/fordelt');

        // We store these values, because we expect them to change soon
        $count_no_setup = $crawler->filter('td:contains("Ikke satt opp")')->count();
        $count_no_answer = $crawler->filter('td:contains("Ingen svar")')->count();
        $count_status = $crawler->filter('td:contains('.$status.')')->count();

        // We need an admin client who is able to schedule an interview
        $client = self::createAdminClient();

        // We need to schedule an interview, and catch the unique code in the email which is sent
        $crawler = $this->goTo('/kontrollpanel/intervju/settopp/6', $client);

        // At this point we are about to send the email
        $form['scheduleInterview[datetime]'] = '10.08.2015 15:00';
        $form = $crawler->selectButton('Send invitasjon på sms og e-post')->form();
        $client->enableProfiler();
        $client->submit($form);


        $response_code = $this->getResponseCodeFromEmail($client);

        $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals($count_no_answer + 1, $crawler->filter('td:contains("Ingen svar")')->count());

        $client = self::createAnonymousClient();
        $crawler = $this->goTo('/intervju/'.$response_code, $client);

        // Clicking a button on this page should trigger the mentioned change.
        $statusButton = $crawler->selectButton($button_text);
        $this->assertNotNull($statusButton);
        $form = $statusButton->form();
        $this->assertNotNull($form);
        $wantEmail = ($status === 'Ny tid ønskes' || $status === 'Kansellert');
        if ($wantEmail) {
            $client->enableProfiler();
        }
        $client->submit($form);

        if ($status === 'Kansellert') {
            $client = $this->helperTestCancelConfirm($client, $response_code);
        } elseif ($status === 'Ny tid ønskes') {
            $crawler = $this->goTo('/intervju/nytid/'.$response_code, $client);
            $form = $crawler->selectButton('Be om nytt tidspunkt')->form();
            $form['InterviewNewTime[newTimeMessage]'] = 'Test answer';
            $client->enableProfiler();
            $client->submit($form);
        }

        if ($wantEmail) {
            $mailCollector = $client->getProfile()->getCollector('swiftmailer');
            $this->assertEquals(1, $mailCollector->getMessageCount());
        }

        $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());

        $crawler = $this->teamMemberGoTo('/kontrollpanel/opptak/fordelt');

        // Verify that a change has taken place.
        $this->assertEquals($count_no_setup - 1, $crawler->filter('td:contains("Ikke satt opp")')->count());
        $this->assertEquals($count_status + 1, $crawler->filter('td:contains('.$status.')')->count());
    }

    /**
     * @param Client $client
     *
     * @return string
     */
    private function getResponseCodeFromEmail(Client $client)
    {
        $mailCollector = $client->getProfile()->getCollector('swiftmailer');
        $this->assertEquals(1, $mailCollector->getMessageCount());
        $message = $mailCollector->getMessages()[0];
        $body = $message->getBody();
        $start = strpos($body, 'intervju/') + 9;
        $messageStartingWithCode = substr($body, $start);
        $end = strpos($messageStartingWithCode, '"');

        return substr($body, $start, $end);
    }

    /**
     * @param Client $client
     * @param string $response_code
     *
     * @return Client
     */
    private function helperTestCancelConfirm(Client $client, string $response_code)
    {
        $crawler = $this->goTo('/intervju/kanseller/tilbakemelding/'.$response_code, $client);
        $form = $crawler->selectButton('Kanseller')->form();
        $form['CancelInterviewConfirmation[message]'] = 'Test answer';
        $client->enableProfiler();
        $client->submit($form);

        $kernel = $this->createKernel();
        $kernel->boot();
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        $interview = $em->getRepository('AppBundle:Interview')->findByResponseCode($response_code);
        $this->assertEquals('Test answer', $interview->getCancelMessage());

        return $client;
    }
}
