<?php


namespace Tests\AppBundle\Controller;

use Tests\BaseWebTestCase;

class FeedbackControllerTest extends BaseWebTestCase
{
    public function testIndex()
    {
        $client = $this->createTeamMemberClient();

        $crawler = $client->request('GET', '/kontrollpanel/feedback');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('button:contains("Send inn")')->count());
        $this->assertEquals(1, $crawler->filter('form')->count());
    }
    //Feature tests the controller and its functionality
    public function testFeature()
    {
        //Sets test-variables
        $testTitle = 'Test-tittel';
        $testDescription = 'Test-beskrivelse';
        $testType = 'question';

        $client = $this->createTeamMemberClient();

        //count current feedbacks
        $client->request('GET', '/kontrollpanel/feedback/list');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $noOfFeedbacksBefore = $this->countTableRows('/kontrollpanel/feedback/list', $client);

        //Submit new feedback
        $crawler = $client->request('GET', '/kontrollpanel/feedback');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //Submits form
        $form = $crawler->selectButton('Send inn')->form([
            'feedback[title]' => $testTitle,
            'feedback[description]' => $testDescription,
            'feedback[type]' => $testType
        ]);
        $client->submit($form);

        //check if new feedback was added
        $noOfFeedbacksAfter = $this->countTableRows('/kontrollpanel/feedback/list', $client);
        $this->assertEquals(1, $noOfFeedbacksAfter - $noOfFeedbacksBefore);

        //check specific feedback
        $crawler = $client->request('GET', '/kontrollpanel/feedback/list');
        $link = $crawler->filter('tbody a')->first()->link();
        $crawler = $client->request('GET', $link->getUri());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //Checks if Title and Description are in the new Feedback
        $this->assertContains($testTitle, $crawler->filter('.card-header')->text());
        $this->assertContains($testDescription, $crawler->filter('#description')->text());
    }

    public function testErrorFeedback()
    {
        //Sets test-variables
        $testTitle = 'Test-error-tittel';
        $testDescription = 'Test-beskrivelse_error';
        $testType = 'question';

        $client = $this->createTeamMemberClient();
        //count current feedbacks
        $client->request('GET', '/kontrollpanel/feedback/list');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $noOfFeedbacksBefore = $this->countTableRows('/kontrollpanel/feedback/list', $client);

        $anonClient = $this->createAnonymousClient();
        //Submit new feedback
        $crawler = $anonClient->request('GET', '/_error/500');
        $this->assertEquals(200, $anonClient->getResponse()->getStatusCode());

        //Submits form
        $form = $crawler->selectButton('Send inn')->form([
            'error_feedback[title]' => $testTitle,
            'error_feedback[description]' => $testDescription
        ]);
        $anonClient->submit($form);

        //check if new feedback was added
        $noOfFeedbacksAfter = $this->countTableRows('/kontrollpanel/feedback/list', $client);
        $this->assertEquals(1, $noOfFeedbacksAfter - $noOfFeedbacksBefore);

        //check specific feedback
        $crawler = $client->request('GET', '/kontrollpanel/feedback/list');
        $link = $crawler->filter('tbody a')->first()->link();
        $crawler = $client->request('GET', $link->getUri());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //Checks if Title and Description are in the new Feedback
        $this->assertContains($testTitle, $crawler->filter('.card-header')->text());
        $this->assertContains($testDescription, $crawler->filter('#description')->text());
    }
}
