<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Role\Roles;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Tests\BaseWebTestCase;

class TwigSurveyControllerTest extends BaseWebTestCase
{
    protected function setUp()
    {
        $this->teamMemberGoTo('/togglepopup');
        $this->teamMemberGoTo('/togglepopup');

        // Admin
        $crawler = $this->adminGoTo('/kontrollpanel/undersokelse/opprett');
        $form = $crawler->filter('button:contains("Lagre")')->form();
        $form['survey[name]'] = "Test123" ;
        $form['survey[showCustomFinishPage]'] = false;
        $form['survey[team_survey]'] = true;
        $form['survey[confidential]'] = false;
        $this->createAdminClient()->submit($form);
    }



    public function testShowPopup()
    {
        $crawler = $this->anonymousGoTo('/');
        $this->assertEquals(0, $crawler->filter('a:contains("undersøkelse!")')->count());
        $crawler = $this->teamMemberGoTo('/');
        $this->assertEquals(1, $crawler->filter('a:contains("undersøkelse!")')->count());

    }

    public function testSendingRemovesPopup(){
        $crawler = $this->teamMemberGoTo('/');
        $this->assertEquals(1, $crawler->filter('a:contains("undersøkelse!")')->count());
        $crawler = $this->teamMemberGoTo($crawler->filter('a:contains("undersøkelse")')->attr('href'));
        $form = $crawler->filter('button:contains("Send")')->form();
        $this->createAdminClient()->submit($form);
        $crawler = $this->teamMemberGoTo('/');
        $this->assertEquals(0, $crawler->filter('a:contains("undersøkelse!")')->count());
    }




    public function testSenereRemovesPopup(){
        $crawler = $this->teamMemberGoTo('/');
        $this->assertEquals(1, $crawler->filter('p:contains("Senere |")')->count());
        $crawler = $this->teamMemberGoTo('/closepopup');
        $crawler = $this->teamMemberGoTo('/');
        $this->assertEquals(0, $crawler->filter('a:contains("undersøkelse!")')->count());
    }


  public function testAldriRemovesPopup(){
      $crawler = $this->teamMemberGoTo('/');
      $this->assertEquals(1, $crawler->filter('p:contains("Aldri")')->count());
      $crawler = $this->teamMemberGoTo('/togglepopup');
      $crawler = $this->teamMemberGoTo('/');
      $this->assertEquals(0, $crawler->filter('a:contains("undersøkelse!")')->count());
      $crawler = $this->teamMemberGoTo('/togglepopup');

  }


  public function testCustomPopUpMessage(){
      $crawler = $this->adminGoTo('/kontrollpanel/undersokelse/opprett');
      $form = $crawler->filter('button:contains("Lagre")')->form();
      $form['survey[name]'] = "Test2" ;
      $form['survey[showCustomFinishPage]'] = false;
      $form['survey[team_survey]'] = true;
      $form['survey[surveyPopUpMessage]'] = "rjwerjlewørwerjweørjewrjwere";
      $form['survey[showCustomPopUpMessage]'] = true;

      $this->createAdminClient()->submit($form);
      $crawler = $this->teamMemberGoTo('/');
      $this->assertEquals(3, $crawler->filter('div:contains("rjwerjlewørwerjweørjewrjwere")')->count());

  }




}
