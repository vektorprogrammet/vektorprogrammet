<?php

namespace Tests\AppBundle\Controller;

use Tests\BaseWebTestCase;

class TwigSurveyControllerTest extends BaseWebTestCase
{
    public function testShowPopup()
    {
        // Admin
        $clientAdmin = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $clientAdmin->request('GET', '/kontrollpanel/undersokelse/opprett');
        $this->assertTrue($clientAdmin->getResponse()->isSuccessful());

        $form['app_bundle_survey_type[name]'] = "Test" ;
        $form['app_bundle_survey_type[showCustomFinishPage]'] = false;
        $form['app_bundle_survey_type[team_survey]'] = true;
        $form['app_bundle_survey_type[confidential]'] = false;

        $clientAnonymous->submit($form);



        $crawler = $this->anonymousGoTo('/');
        $this->assertEquals(0, $crawler->filter('p:contains("undersøkelse")')->count());

        $crawler = $this->teamMemberGoTo('/');
        $this->assertEquals(1, $crawler->filter('p:contains("undersøkelse")')->count());

    }




}
