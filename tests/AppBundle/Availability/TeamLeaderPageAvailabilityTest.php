<?php

namespace Tests\App\Availability;

use Tests\BaseWebTestCase;

class TeamLeaderPageAvailabilityTest extends BaseWebTestCase
{
    /**
     * @dataProvider urlProvider
     */
    public function testPageIsSuccessful(string $url): void
    {
        $client = $this->createTeamLeaderClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * @dataProvider urlProvider
     */
    public function testPageIsDeniedForTeamMember(string $url): void
    {
        $client = $this->createTeamMemberClient();
        $client->request('GET', $url);

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function urlProvider(): array
    {
        return [
            ['/kontrollpanel/intervju/settopp/6'],
            ['/kontrollpanel/intervju/conduct/6'],
            ['/kontrollpanel/intervju/vis/4'],
            ['/kontrollpanel/skole/timeplan/'],

            ['/kontrollpanel/teamadmin/stillinger'],
            ['/kontrollpanel/teamadmin/opprett/stilling'],
            ['/kontrollpanel/teamadmin/rediger/stilling/1'],
            ['/kontrollpanel/teamadmin/avdeling/opprett/1'],
            ['/kontrollpanel/teamadmin/update/1'],
            ['/kontrollpanel/teamadmin/team/nytt_medlem/1'],
            ['/kontrollpanel/teamadmin/oppdater/teamhistorie/1'],
            ['/kontrollpanel/team/avdeling/2'],

            ['/kontrollpanel/hovedstyret'],
            ['/kontrollpanel/hovedstyret/nytt_medlem/1'],
            ['/kontrollpanel/hovedstyret/rediger_medlem/1'],
            ['/kontrollpanel/hovedstyret/oppdater'],

            ['/kontrollpanel/opptakadmin/teaminteresse'],
            ['/kontrollpanel/opptakadmin/teaminteresse?department=1&semester=1'],

            ['/kontrollpanel/brukeradmin/avdeling/2'],
            ['/kontrollpanel/brukeradmin/opprett/2'],

            ['/kontrollpanel/avdelingadmin/update/1'],

            ['/kontrollpanel/skoleadmin/opprett/1'],
            ['/kontrollpanel/skoleadmin/oppdater/1'],
            ['/kontrollpanel/skoleadmin/avdeling/2'],

            ['/kontrollpanel/linjer'],
            ['/kontrollpanel/linje/1'],
            ['/kontrollpanel/linje'],
        ];
    }
}
