<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;

class InfoMeetingControllerTest extends BaseWebTestCase
{
    public function testCreateMeeting()
    {
        $crawler = $this->anonymousGoTo('/opptak/avdeling/1');

        $before = $crawler->filter('p:contains("Husk infomøte")')->count();

        $crawler = $this->teamLeaderGoTo('/kontrollpanel/semesteradmin/update/2');

        $this->assertEquals(1, $crawler->filter('h1:contains("Endre opptaksperiode")')->count());

        $saveButton = $crawler->filter('button:contains("Endre")');
        $this->assertNotNull($saveButton);

        $form = $saveButton->form();
        $this->assertNotNull($form);

        $form['createSemester[infoMeeting][showOnPage]'] = true;
        $form['createSemester[infoMeeting][date]'] = (new \DateTime())->modify('+1day')->format('d.m.Y H:i');
        $form['createSemester[infoMeeting][room]'] = 'Parken';
        $form['createSemester[infoMeeting][description]'] = 'Forvent mat og drikke!';
        $this->createTeamLeaderClient()->submit($form);

        $crawler = $this->anonymousGoTo('/opptak/avdeling/1');
        $after = $crawler->filter('p:contains("Husk infomøte")')->count();
//      TODO: finish this test when info meeting is reimplemented
//        $this->assertEquals($before + 1, $after);
    }
}
