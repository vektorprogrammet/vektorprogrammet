<?php

namespace Tests\AppBundle\Availability;

use Tests\BaseWebTestCase;

class AvailabilityFunctionalTest extends BaseWebTestCase
{
    /**
     * @dataProvider publicUrlProvider
     *
     * @param $url
     */
    public function testPublicPageIsSuccessful($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * @dataProvider assistantUrlProvider
     *
     * @param $url
     */
    public function testAssistantPageIsSuccessful($url)
    {
        $client = $this->createAssistantClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * @dataProvider assistantUrlProvider
     *
     * @param $url
     */
    public function testAssistantPageIsDenied($url)
    {
        //Check if anonymous users gets denied
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider teamMemberUrlProvider
     *
     * @param $url
     */
    public function testTeamMemberPageIsSuccessful($url)
    {
        $client = $this->createTeamMemberClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * @dataProvider teamMemberUrlProvider
     *
     * @param $url
     */
    public function testTeamMemberPageIsDenied($url)
    {
        //Check if assistants gets denied
        $client = $this->createAssistantClient();
        $client->request('GET', $url);

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider teamLeaderUrlProvider
     *
     * @param $url
     */
    public function testTeamLeaderPageIsSuccessful($url)
    {
        $client = $this->createTeamLeaderClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * @dataProvider teamLeaderUrlProvider
     *
     * @param $url
     */
    public function testTeamLeaderPageIsDenied($url)
    {
        //Check if team member gets denied
        $client = $this->createTeamMemberClient();
        $client->request('GET', $url);

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider adminUrlProvider
     *
     * @param $url
     */
    public function testAdminPageIsSuccessful($url)
    {
        $client = $this->createAdminClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * @dataProvider adminUrlProvider
     *
     * @param $url
     */
    public function testAdminPageIsDenied($url)
    {
        //Check if team leader gets denied
        $client = $this->createTeamLeaderClient();
        $client->request('GET', $url);

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function publicUrlProvider()
    {
        return array(
            array('/'),
            array('/assistenter'),
            array('/team'),
            array('/laerere'),
            array('/omvektor'),
            array('/kontakt'),

            array('/nyheter'),
            array('/nyheter/ntnu'),
            array('/nyhet/1'),

            array('/profile/1'),

            array('/opptak'),
            array('/opptak/NTNU'),
            array('/opptak/avdeling/1'),
            array('/opptak/Bergen'),
            array('/opptak/ås'),

            array('/avdeling/Trondheim'),
            array('/avdeling/NTNU'),
            array('/avdeling/ås'),
        );
    }

    public function assistantUrlProvider()
    {
        return array(
            array('/profile'),
            array('/min-side'),
            array('/utlegg'),
        );
    }

    public function teamMemberUrlProvider()
    {
        return array(
            array('/kontrollpanel'),
            array('/kontrollpanel/semesteradmin'),

            array('/kontrollpanel/opptak/nye'),
            array('/kontrollpanel/opptak/nye/4'),
            array('/kontrollpanel/opptak/gamle'),
            array('/kontrollpanel/opptak/gamle/4'),
            array('/kontrollpanel/opptak/fordelt'),
            array('/kontrollpanel/opptak/fordelt/4'),
            array('/kontrollpanel/opptak/intervjuet'),
            array('/kontrollpanel/opptak/intervjuet/4'),

            array('/kontrollpanel/stand'),
            array('/kontrollpanel/stand/4'),

            array('/kontrollpanel/statistikk/opptak'),
            array('/kontrollpanel/statistikk/opptak/4'),

            array('/kontrollpanel/deltakerhistorikk'),
            array('/kontrollpanel/deltakerhistorikk/4'),

            array('/kontrollpanel/vikar'),
            array('/kontrollpanel/vikar/semester/4'),

            array('/kontrollpanel/team/avdeling'),
            array('/kontrollpanel/teamadmin/team/1'),

            array('/kontrollpanel/brukeradmin'),
            array('/kontrollpanel/epostlister'),
            array('/kontrollpanel/sponsorer'),

            array('/kontrollpanel/utlegg'),
            array('/kontrollpanel/utlegg/2'),

            array('/kontrollpanel/undersokelse/admin'),
            array('/kontrollpanel/undersokelse/admin/4'),
            array('/kontrollpanel/undersokelse/opprett'),

            array('/kontrollpanel/artikkeladmin'),
            array('/kontrollpanel/artikkeladmin/opprett'),
            array('/kontrollpanel/artikkeladmin/rediger/1'),

            array('/kontrollpanel/avdelingadmin'),

            array('/kontrollpanel/skoleadmin'),
            array('/kontrollpanel/skoleadmin/brukere'),
            array('/kontrollpanel/skoleadmin/tildel/skole/1'),
        );
    }

    public function teamLeaderUrlProvider()
    {
        return array(
            array('/kontrollpanel/semesteradmin/avdeling/2'),
            array('/kontrollpanel/semesteradmin/avdeling/opprett/1'),

            array('/kontrollpanel/intervju/settopp/6'),
            array('/kontrollpanel/intervju/conduct/6'),
            array('/kontrollpanel/intervju/vis/4'),
            array('/kontrollpanel/intervju/skjema'),
            array('/kontrollpanel/intervju/skjema/1'),
            array('/kontrollpanel/skole/timeplan/'),

            array('/kontrollpanel/teamadmin/stillinger'),
            array('/kontrollpanel/teamadmin/opprett/stilling'),
            array('/kontrollpanel/teamadmin/rediger/stilling/1'),
            array('/kontrollpanel/teamadmin/avdeling/opprett/1'),
            array('/kontrollpanel/teamadmin/update/1'),
            array('/kontrollpanel/teamadmin/team/nytt_medlem/1'),
            array('/kontrollpanel/teamadmin/oppdater/teamhistorie/1'),
            array('/kontrollpanel/team/avdeling/2'),

            array('/kontrollpanel/hovedstyret'),
            array('/kontrollpanel/hovedstyret/nytt_medlem/1'),
            array('/kontrollpanel/hovedstyret/rediger_medlem/1'),
            array('/kontrollpanel/hovedstyret/oppdater'),

            array('/kontrollpanel/opptakadmin/teaminteresse'),
            array('/kontrollpanel/opptakadmin/teaminteresse/4'),

            array('/kontrollpanel/brukeradmin/avdeling/2'),
            array('/kontrollpanel/brukeradmin/opprett/2'),

            array('/kontrollpanel/avdelingadmin/update/1'),

            array('/kontrollpanel/skoleadmin/opprett/1'),
            array('/kontrollpanel/skoleadmin/oppdater/1'),
            array('/kontrollpanel/skoleadmin/avdeling/2'),

            array('/kontrollpanel/linjer'),
            array('/kontrollpanel/linje/1'),
            array('/kontrollpanel/linje'),
        );
    }

    public function adminUrlProvider()
    {
        return array(
            array('/kontrollpanel/staging'),
            array('/kontrollpanel/avdelingadmin/opprett'),
            array('/kontrollpanel/bruker/vekorepost/endre/1'),
            array('/kontrollpanel/admin/accessrules'),
            array('/kontrollpanel/admin/accessrules/create'),
            array('/kontrollpanel/admin/accessrules/routing/create'),
        );
    }
}
