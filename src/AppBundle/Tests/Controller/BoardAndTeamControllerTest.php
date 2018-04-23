<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;

class BoardAndTeamControllerTest extends BaseWebTestCase
{
    public function testShow()
    {
        $crawler = $this->goTo('/team');
        $this->assertEquals(1, $crawler->filter('h1:contains("team")')->count());

        $teamOverview = $crawler->filter('.team-overview');
        $tabs = $teamOverview->filter('#team-overview-tabs');
        $this->assertEquals(5, $tabs->filter('a')->count());
        $this->assertGreaterThan(5, $teamOverview->filter('.team-card')->count());
    }
}
