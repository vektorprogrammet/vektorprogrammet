<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;
use Symfony\Component\Debug\Exception\ContextErrorException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Client;

class ReceiptControllerTest extends BaseWebTestCase
{
    public function testCreate()
    {
        // Assistant creates a receipt
        $crawler = $this->fillAndSubmitForm($this->createAssistantClient(), '/utlegg');
        $this->assertEquals(1, $crawler->filter('h3:contains("Nye utlegg")')->count());

        // Get id for later deletion
        $editHref = explode('/', $crawler->filter('a:contains("Rediger")')->attr('href'));
        $receiptId = end($editHref);

        // Teamleader can see it in the receipt table
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/utlegg');
        $this->assertEquals(1, $crawler->filter('td:contains("assistant@gmail.com")')->count());

        // Delete it just to get rid of the image file
        $this->createAssistantClient()->request('POST', '/utlegg/slett/'.$receiptId);
    }

    public function testFinish()
    {
        $client = $this->createTeamLeaderClient();
        $client->request('POST', '/kontrollpanel/utlegg/ferdig/2');
        $this->assertEquals(302, $client->getResponse()->getStatusCode()); // Successful if redirect
        $client->request('POST', '/kontrollpanel/utlegg/ferdig/2'); // Try again with same receipt
        $this->assertEquals(400, $client->getResponse()->getStatusCode()); // Controller throws 400
    }

    public function testEdit()
    {
        // Teamleader edits
        $client = $this->createTeamLeaderClient();

        // Create a new image file
        $file = tempnam(sys_get_temp_dir(), 'rec');
        imagepng(imagecreatetruecolor(1, 1), $file);

        $photo = new UploadedFile($file, 'receipt.png', null, null, null, true);

        $crawler = $client->request('GET', '/utlegg/rediger/2');
        $form = $crawler->selectButton('Lagre')->form();

        $form['receipt[description]'] = 'foo bar';
        $form['receipt[sum]'] = 999;
        $form['receipt[picturePath]'] = $photo; // We have to upload a photo otherwise bad stuff happens

        $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertEquals(1, $crawler->filter('td:contains("foo bar")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("999,00,-")')->count());
    }

    public function testDelete()
    {
        // Teamleader deletes
        $client = $this->createTeamLeaderClient();
        $client->request('POST', '/utlegg/slett/2');
        $this->assertEquals(302, $client->getResponse()->getStatusCode()); // Successful if redirected
        $client->request('POST', '/utlegg/slett/2'); // Try to delete same receipt again
        $this->assertEquals(404, $client->getResponse()->getStatusCode()); // Doesn't exist
    }

    public function testPermissions()
    {
        // Anonymous has no access
        $client = $this->createAnonymousClient();
        $client->request('GET', '/utlegg');
        $crawler = $client->followRedirect();
        $this->assertEquals(1, $crawler->filter('button:contains("login")')->count());

        /* -- Create receipts for each role (except highest admin which has same permission as teamleader) -- */

        // Assistant
        $crawler = $this->fillAndSubmitForm($this->createAssistantClient(), '/utlegg');
        $this->assertEquals(1, $crawler->filter('h3:contains("Nye utlegg")')->count());
        $editHref = explode('/', $crawler->filter('a:contains("Rediger")')->attr('href'));
        $assistantReceiptId = end($editHref);

        // Team member
        $crawler = $this->fillAndSubmitForm($this->createTeamMemberClient(), '/utlegg');
        $this->assertEquals(1, $crawler->filter('h3:contains("Nye utlegg")')->count());
        $editHref = explode('/', $crawler->filter('a:contains("Rediger")')->attr('href'));
        $teamMemberReceiptId = end($editHref);

        // Team leader
        $crawler = $this->fillAndSubmitForm($this->createTeamLeaderClient(), '/utlegg');
        $this->assertEquals(1, $crawler->filter('h3:contains("Nye utlegg")')->count());
        $editHref = explode('/', $crawler->filter('a:contains("Rediger")')->attr('href'));
        $teamLeaderReceiptId = end($editHref);

        /* -- Try to finish each receipt -- */

        // Skip assistant - no access to control panel
        // Team member has no access
        $client = $this->createTeamMemberClient();
        $client->request('POST', '/kontrollpanel/utlegg/ferdig/' . $teamMemberReceiptId);
        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        // Team leader
        $client = $this->createTeamLeaderClient();
        $client->request('POST', '/kontrollpanel/utlegg/ferdig/' . $assistantReceiptId);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->request('POST', '/kontrollpanel/utlegg/ferdig/' . $teamMemberReceiptId);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->request('POST', '/kontrollpanel/utlegg/ferdig/' . $teamLeaderReceiptId);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        /* -- Try to edit each own receipt -- */

        // Assistant
        $client = $this->createAssistantClient();
        $client->request('GET', '/utlegg/rediger/' . $assistantReceiptId);
        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        // Team member
        $client = $this->createTeamMemberClient();
        $client->request('GET', '/utlegg/rediger/' . $teamMemberReceiptId);
        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        // Team leader can access all
        $this->teamLeaderGoTo('/utlegg/rediger/' . $assistantReceiptId);
        $this->teamLeaderGoTo('/utlegg/rediger/' . $teamMemberReceiptId);
        $this->teamLeaderGoTo('/utlegg/rediger/' . $teamLeaderReceiptId);

        /* -- Try to delete each own receipt -- */

        // Assistant
        $client = $this->createAssistantClient();
        $client->request('POST', '/utlegg/slett/' . $assistantReceiptId);
        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        // Team member
        $client = $this->createTeamMemberClient();
        $client->request('POST', '/utlegg/slett/' . $teamMemberReceiptId);
        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        // Team leader can delete all
        $client = $this->createTeamLeaderClient();
        $client->request('POST', '/utlegg/slett/' . $assistantReceiptId);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->request('POST', '/utlegg/slett/' . $teamMemberReceiptId);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->request('POST', '/utlegg/slett/' . $teamLeaderReceiptId);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        \TestDataManager::restoreDatabase();
    }

    private function fillAndSubmitForm(Client $client, $uri)
    {
        // Create a new image file
        $file = tempnam(sys_get_temp_dir(), 'rec');
        imagepng(imagecreatetruecolor(1, 1), $file);

        $photo = new UploadedFile($file, 'receipt.png', null, null, null, true);

        $crawler = $client->request('GET', $uri);
        $form = $crawler->selectButton('Registrer')->form();

        $form['receipt[description]'] = 'En flott beskrivelse';
        $form['receipt[sum]'] = 123;
        $form['receipt[user][account_number]'] = '1234.56.78903';
        $form['receipt[picturePath]'] = $photo;

        $client->submit($form);
        return $client->followRedirect();
    }

    protected function tearDown()
    {
        try {
            rmdir('images/receipts');
            rmdir('images');
        } catch (ContextErrorException $e) {
            // The directory is not empty because the receipts have not been deleted yet
        }
    }
}
