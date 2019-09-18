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
    //    dump($crawler->filter('div:contains("Send en melding til IT-teamet!"'));
        $this->assertEquals(1, $crawler->filter('button:contains("Send inn")')->count());
        $this->assertEquals(1, $crawler->filter('form')->count());
    }
    public function testShow()
    {
        $client = $this->createTeamMemberClient();

        //count feedbacks
        $crawler1 = $client->request('GET', '/kontrollpanel/feedback/list');
        $noOfFeedbacksBefore = $this->countTableRows('/kontrollpanel/feedback/list', $client);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //Submit new feedback
        $crawler2 = $client->request('GET', '/kontrollpanel/feedback');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler2->selectButton('Send inn')->form([
            'feedback[title]' => 'Test-tittel',
            'feedback[description]' => 'Test-beskrivelse',
            'feedback[type]' => 'question'
        ]);
        $client->submit($form);
        //check if feedbacks++

        $crawler3 = $client->request('GET', '/kontrollpanel/feedback/list');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $noOfFeedbacksAfter = $this->countTableRows('/kontrollpanel/feedback/list', $client);
        $this->assertEquals(1, $noOfFeedbacksAfter - $noOfFeedbacksBefore);

        //check specific feedback
    }
}

