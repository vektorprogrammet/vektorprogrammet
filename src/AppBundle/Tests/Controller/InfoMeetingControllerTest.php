<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;

class InfoMeetingControllerTest extends BaseWebTestCase
{
        public function testCreateMeeting()
        {
            $client = $this->createTeamMemberClient();
            $crawler = $this->goTo('/opptak/avdeling/1', $client);

            $before = $crawler->filter('p:contains("Husk infomøte")')->count();

            $crawler = $this->goTo('/kontrollpanel/infomøte/1', $client);

            $saveButton = $crawler->filter('button#edit_info_meeting_save');
            $this->assertNotNull($saveButton);

            $form = $saveButton->form();
            $this->assertNotNull($form);

            $form['edit_info_meeting[date]'] = "05/06";
            $form['edit_info_meeting[time]'] = '17:00';
            $form['edit_info_meeting[room]'] = 'Parken';
            $form['edit_info_meeting[extra]'] = 'Forvent mat og drikke!';
            $client->submit($form);
            $client->followRedirect();

            $crawler = $this->goTo('/opptak/avdeling/1', $client);
            $after = $crawler->filter('p:contains("Husk infomøte")')->count();

            $this->assertEquals($before + 1, $after);
        }

        public function testDeleteMeeting()
        {
            $client = $this->createTeamMemberClient();
            $crawler = $this->goTo('/opptak/avdeling/3', $client);

            $before = $crawler->filter('p:contains("Husk infomøte")')->count();

            $crawler = $this->goTo('/kontrollpanel/infomøte/3', $client);

            $deleteButton = $crawler->filter('button:contains("Slett")');
            $this->assertNotNull($deleteButton);

            $form = $deleteButton->form();
            $this->assertNotNull($form);

            $client->submit($form);
            $client->followRedirect();

            $crawler = $this->goTo('/opptak/avdeling/3', $client);
            $after = $crawler->filter('p:contains("Husk infomøte")')->count();

            $this->assertEquals($before - 1, $after);
        }
}
