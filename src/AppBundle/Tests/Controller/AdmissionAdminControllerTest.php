<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;

class AdmissionAdminControllerTest extends BaseWebTestCase
{
    public function testShowAsTeamMember()
    {
        $crawler = $this->teamMemberGoTo('/kontrollpanel/opptak');

        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());
        $this->assertEquals(0, $crawler->filter('a.button:contains("Ny søker")')->count());
        $this->assertEquals(0, $crawler->filter('td>a.button:contains("Slett")')->count());
        $this->assertEquals(0, $crawler->filter('td>a.button:contains("Fordel")')->count());
    }

    public function testShowAsTeamLeader()
    {
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/opptak');

        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());
        $this->assertEquals(3, $crawler->filter('a:contains("Avdeling")')->count());
        $this->assertEquals(3, $crawler->filter('a:contains("Semester")')->count());
        $this->assertEquals(0, $crawler->filter('td>a:contains("Slett")')->count());
        $this->assertEquals(3, $crawler->filter('a:contains("Fordel")')->count());
    }

    public function testShowAsAdmin()
    {
        $crawler = $this->adminGoTo('/kontrollpanel/opptak');

        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());
        $this->assertEquals(3, $crawler->filter('a:contains("Avdeling")')->count());
        $this->assertEquals(3, $crawler->filter('a:contains("Semester")')->count());
        $this->assertEquals(2, $crawler->filter('td>a:contains("Slett")')->count());
        $this->assertEquals(2, $crawler->filter('td>a:contains("Fordel")')->count());
    }

    public function testAssignedAsTeamMember()
    {
        $crawler = $this->teamMemberGoTo('/kontrollpanel/opptak/fordelt');

        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());
        $this->assertEquals(0, $crawler->filter('td>a:contains("Sett opp")')->count());
        $this->assertEquals(0, $crawler->filter('td>a:contains("Intervju")')->count());
    }

    public function testAssignedAsTeamLeader()
    {
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/opptak/fordelt');

        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());
        $this->assertEquals(3, $crawler->filter('a:contains("Avdeling")')->count());
        $this->assertEquals(3, $crawler->filter('a:contains("Semester")')->count());
        $this->assertEquals(2, $crawler->filter('td>a:contains("Sett opp")')->count());
        $this->assertEquals(2, $crawler->filter('td>a:contains("Intervju")')->count());
    }

    public function testAssignedAsAdmin()
    {
        $crawler = $this->adminGoTo('/kontrollpanel/opptak/fordelt');

        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());
        $this->assertEquals(3, $crawler->filter('a:contains("Avdeling")')->count());
        $this->assertEquals(3, $crawler->filter('a:contains("Semester")')->count());
        $this->assertEquals(2, $crawler->filter('td>a:contains("Sett opp")')->count());
        $this->assertEquals(2, $crawler->filter('td>a:contains("Intervju")')->count());
    }

    public function testInterviewedAsTeamMember()
    {
        $crawler = $this->teamMemberGoTo('/kontrollpanel/opptak/intervjuet');

        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());
        $this->assertEquals(0, $crawler->filter('td>a:contains("Les intervju")')->count());
        $this->assertEquals(0, $crawler->filter('td>a:contains("Slett")')->count());
    }

    public function testInterviewedAsTeamLeader()
    {
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/opptak/intervjuet');

        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());
        $this->assertEquals(3, $crawler->filter('a:contains("Avdeling")')->count());
        $this->assertEquals(3, $crawler->filter('a:contains("Semester")')->count());
        $this->assertEquals(0, $crawler->filter('td>a.button.tiny:contains("Slett")')->count());
        $this->assertEquals(2, $crawler->filter('td>a.button.tiny:contains("Les intervju")')->count());
    }

    public function testInterviewedAsAdmin()
    {
        $crawler = $this->adminGoTo('/kontrollpanel/opptak/intervjuet');

        $this->assertEquals(1, $crawler->filter('h1:contains("Opptak")')->count());
        $this->assertEquals(3, $crawler->filter('a:contains("Avdeling")')->count());
        $this->assertEquals(3, $crawler->filter('a:contains("Semester")')->count());
        $this->assertEquals(2, $crawler->filter('td>a.button:contains("Les intervju")')->count());
        $this->assertEquals(2, $crawler->filter('td>a.button:contains("Slett")')->count());
    }

    public function testAssistantIsDenied()
    {
        $client = self::createAssistantClient();

        $client->request('GET', '/kontrollpanel/opptak');

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

    /**
     * Assert that no email is sent when we click on 'Lagre tidspunkt'.
     */
    public function testSaveAndNoEmail()
    {
        $client = self::createAdminClient();
        $crawler = $this->goTo('/kontrollpanel/intervju/settopp/6', $client);
        $form['scheduleInterview[datetime]'] = '2015-08-10 15:00:00';
        $form = $crawler->selectButton('Lagre tidspunkt')->form();
        $client->enableProfiler();
        $client->submit($form);

        $mailCollector = $client->getProfile()->getCollector('swiftmailer');
        $this->assertEquals(0, $mailCollector->getMessageCount());
    }

    /**
     * Test the functions on /intervju/code.
     */
    public function testResponseInterview()
    {
        // Test accept
        $this->helperTestStatus('Akseptert', 'Godta', 'Intervjuet ble akseptert.');

        // Test reschedule
        $this->helperTestStatus('Ny tid ønskes', 'Be om ny tid', 'Forespørsel har blitt sendt.');

        // Test cancel
        // TODO
    }

    /**
     * Test the status functionality on /intervju/code.
     *
     * Start at kontrollpanel/opptak/fordelt and count occurrences of $status and "Ingen
     * svar". Then, set up an interview and arrange for an email to be sent to the candidate.
     * Examine the contents of the email and extract the unique response code. Proceed to the
     * schedule response page with our special code and click the button corresponding to
     * $button_text. Then, verify that we get the correct flash message after the redirect. Finally,
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
        $count_no_answer = $crawler->filter('td:contains("Ingen svar")')->count();
        $count_status = $crawler->filter('td:contains('.$status.')')->count();

        // We need an admin client who is able to schedule an interview
        $client = self::createAdminClient();

        // We need to schedule an interview, and catch the unique code in the email which is sent
        $crawler = $this->goTo('/kontrollpanel/intervju/settopp/6', $client);

        // At this point we are about to send the email
        $form['scheduleInterview[datetime]'] = '2015-08-10 15:00:00';
        $form = $crawler->selectButton('Lagre tidspunkt og send mail')->form();
        $client->enableProfiler();
        $client->submit($form);

        $response_code = $this->getResponseCodeFromEmail($client);

        $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());

        $client = self::createAnonymousClient();
        $crawler = $this->goTo('/intervju/'.$response_code, $client);

        // Clicking a button on this page should trigger the mentioned change.
        $statusButton = $crawler->selectButton($button_text);
        $form = $statusButton->form();
        $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertTrue($client->getResponse()->isSuccessful());

        $filter_string = "div:contains('".$flash_text."')";
        $this->assertEquals(4, $crawler->filter($filter_string)->count());

        $crawler = $this->teamMemberGoTo('/kontrollpanel/opptak/fordelt');

        // Verify that a change has taken place.
        $this->assertEquals($count_no_answer - 1, $crawler->filter('td:contains("Ingen svar")')->count());
        $this->assertEquals($count_status + 1, $crawler->filter('td:contains('.$status.')')->count());

        \TestDataManager::restoreDatabase();
    }

    private function getResponseCodeFromEmail($client)
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
}
