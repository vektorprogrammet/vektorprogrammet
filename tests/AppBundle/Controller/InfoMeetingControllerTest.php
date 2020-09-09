<?php

namespace Tests\AppBundle\Controller;

use DateTime;
use Tests\BaseWebTestCase;

class InfoMeetingControllerTest extends BaseWebTestCase
{
    public function testCreateMeeting()
    {
        $crawler = $this->anonymousGoTo('/opptak/avdeling/1');

        $before = $crawler->filter('p:contains("Husk infomøte")')->count();

        $crawler = $this->teamLeaderGoTo('/kontrollpanel/opptaksperiode/update/2');

        $saveButton = $crawler->filter('button:contains("Lagre")');
        $this->assertNotNull($saveButton);

        $form = $saveButton->form();
        $this->assertNotNull($form);

        $form['editAdmissionPeriod[infoMeeting][showOnPage]'] = true;
        $form['editAdmissionPeriod[infoMeeting][date]'] = (new DateTime())->modify('+1day')->format('d.m.Y H:i');
        $form['editAdmissionPeriod[infoMeeting][room]'] = 'Parken';
        $form['editAdmissionPeriod[infoMeeting][description]'] = 'Forvent mat og drikke!';
        $this->createTeamLeaderClient()->submit($form);

        $crawler = $this->anonymousGoTo('/opptak/avdeling/1');
        $after = $crawler->filter('p:contains("Husk infomøte")')->count();
//      TODO: finish this test when info meeting is reimplemented
//        $this->assertEquals($before + 1, $after);
    }
}
