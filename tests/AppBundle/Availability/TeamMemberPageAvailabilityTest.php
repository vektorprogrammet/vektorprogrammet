<?php

namespace Tests\App\Availability;

use Tests\BaseWebTestCase;

class TeamMemberPageAvailabilityTest extends BaseWebTestCase
{
    /**
     * @dataProvider urlProvider
     */
    public function testPageIsSuccessful(string $url): void
    {
        $client = $this->createTeamMemberClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * @dataProvider urlProvider
     */
    public function testPageIsDeniedForAssistant(string $url): void
    {
        $client = $this->createAssistantClient();
        $client->request('GET', $url);

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function urlProvider(): array
    {
        return [
            ['/kontrollpanel'],
            ['/kontrollpanel/opptaksperiode'],

            ['/kontrollpanel/opptak/nye'],
            ['/kontrollpanel/opptak/nye?department=1&semester=1'],
            ['/kontrollpanel/opptak/gamle'],
            ['/kontrollpanel/opptak/gamle?department=1&semester=1'],
            ['/kontrollpanel/opptak/fordelt'],
            ['/kontrollpanel/opptak/fordelt?department=1&semester=1'],
            ['/kontrollpanel/opptak/intervjuet'],
            ['/kontrollpanel/opptak/intervjuet?department=1&semester=1'],

            ['/kontrollpanel/intervju/skjema'],
            ['/kontrollpanel/intervju/skjema/1'],

            ['/kontrollpanel/stand'],
            ['/kontrollpanel/stand?department=1&semester=1'],

            ['/kontrollpanel/statistikk/opptak'],
            ['/kontrollpanel/statistikk/opptak?department=1&semester=1'],

            ['/kontrollpanel/deltakerhistorikk'],
            ['/kontrollpanel/deltakerhistorikk?department=1&semester=1'],

            ['/kontrollpanel/vikar'],
            ['/kontrollpanel/vikar?department=1&semester=1'],

            ['/kontrollpanel/team/avdeling'],
            ['/kontrollpanel/teamadmin/team/1'],

            ['/kontrollpanel/opprettsoker'],
            ['/kontrollpanel/brukeradmin/opprett'],

            ['/kontrollpanel/artikkeladmin'],

            ['/kontrollpanel/vikar'],
            ['/kontrollpanel/vikar?department=1&semester=1'],

            ['/kontrollpanel/team/avdeling'],
            ['/kontrollpanel/teamadmin/team/1'],

            ['/kontrollpanel/brukeradmin'],
            ['/kontrollpanel/epostlister'],
            ['/kontrollpanel/sponsorer'],

            ['/kontrollpanel/utlegg'],
            ['/kontrollpanel/utlegg/2'],

            ['/kontrollpanel/undersokelse/admin'],
            ['/kontrollpanel/undersokelse/admin?department=1&semester=1'],
            ['/kontrollpanel/undersokelse/opprett'],

            ['/kontrollpanel/artikkeladmin'],
            ['/kontrollpanel/artikkeladmin/opprett'],
            ['/kontrollpanel/artikkeladmin/rediger/1'],

            ['/kontrollpanel/avdelingadmin'],

            ['/kontrollpanel/skoleadmin'],
            ['/kontrollpanel/skoleadmin/brukere'],
            ['/kontrollpanel/skoleadmin/tildel/skole/1'],
        ];
    }
}
