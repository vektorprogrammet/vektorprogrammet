<?php

namespace Tests\App\Availability;

use Tests\BaseWebTestCase;

class AdminPageAvailabilityTest extends BaseWebTestCase
{
    /**
     * @dataProvider urlProvider
     */
    public function testPageIsSuccessful(string $url): void
    {
        $client = $this->createAdminClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * @dataProvider urlProvider
     */
    public function testPageIsDeniedForTeamLeader(string $url): void
    {
        $client = $this->createTeamLeaderClient();
        $client->request('GET', $url);

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function urlProvider(): array
    {
        return [
            ['/kontrollpanel/avdelingadmin/opprett'],
            ['/kontrollpanel/bruker/vekorepost/endre/1'],
            ['/kontrollpanel/semesteradmin'],
            ['/kontrollpanel/semesteradmin/opprett'],
            ['/kontrollpanel/admin/accessrules'],
            ['/kontrollpanel/admin/accessrules/create'],
            ['/kontrollpanel/admin/accessrules/routing/create'],
        ];
    }
}
