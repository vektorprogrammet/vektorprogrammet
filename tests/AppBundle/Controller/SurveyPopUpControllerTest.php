<?php

namespace Tests\AppBundle\Controller;

use Tests\BaseWebTestCase;

class SurveyPopUpControllerTest extends BaseWebTestCase
{
    protected function setUp()
    {
        $client = $this->createTeamMemberClient();
        $client->request('POST', '/togglepopup');
        $client->request('POST', '/togglepopup');
    }


    public function testShowPopup()
    {
        $crawler = $this->anonymousGoTo('/');
        $this->assertEquals(0, $crawler->filter('div:contains("Svar på undersøkelse!")')->count());
        $crawler = $this->teamMemberGoTo('/');
        $this->assertEquals(3, $crawler->filter('div:contains("Svar på undersøkelse!")')->count());
    }

    public function testSendingRemovesPopup(){
        $crawler = $this->teamMemberGoTo('/');
        $this->assertEquals(1, $crawler->filter(".survey-link")->count());
        $crawler = $this->teamMemberGoTo($crawler->filter(".survey-link")->attr('href'));
        $form = $crawler->filter('button:contains("Send")')->form();
        $this->createTeamMemberClient()->submit($form);
        $crawler = $this->teamMemberGoTo('/');
        $this->assertEquals(0, $crawler->filter('div:contains("Svar på undersøkelse!")')->count());
    }

    public function testSenereRemovesPopup(){
        $crawler = $this->teamMemberGoTo('/');
        $this->assertEquals(1, $crawler->filter('p:contains("Senere |")')->count());
        $this->createTeamMemberClient()->request('POST', '/closepopup');
        $crawler = $this->teamMemberGoTo('/');
        $this->assertEquals(0, $crawler->filter('p:contains("Svar på undersøkelse!")')->count());
    }


  public function testAldriRemovesPopup(){
      $crawler = $this->teamMemberGoTo('/');
      $this->assertEquals(1, $crawler->filter('p:contains("Aldri")')->count());
      $this->createTeamMemberClient()->request('POST', '/togglepopup');
      $crawler = $this->teamMemberGoTo('/');
      $this->assertEquals(0, $crawler->filter('a:contains("Svar på undersøkelse!")')->count());
      $this->createTeamMemberClient()->request('POST', '/togglepopup');

  }

}
