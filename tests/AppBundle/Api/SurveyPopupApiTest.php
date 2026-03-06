<?php

namespace Tests\AppBundle\Api;

use Tests\BaseWebTestCase;

class SurveyPopupApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    public function testSurveyPopupRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/surveys/popup', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testSurveyPopupReturnsSurveyDataForTeamMember(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/surveys/popup', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(200);

        $data = json_decode($client->getResponse()->getContent(), true);

        // teammember has ROLE_TEAM_MEMBER, lastPopUpTime is 2000-01-01 (>1 day ago),
        // reservedFromPopUp is false, and team surveys exist in fixtures for the
        // current semester -- so the response should contain survey data.
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('name', $data);
        $this->assertNotNull($data['id'], 'Survey id should not be null for eligible team member');
        $this->assertNotNull($data['name'], 'Survey name should not be null for eligible team member');
    }

    public function testSurveyPopupReturnsEmptyForRegularUser(): void
    {
        // assistent has ROLE_USER only (not ROLE_TEAM_MEMBER), so the
        // team member guard in SurveyPopupProvider should return empty data.
        $token = $this->getJwtToken('assistent', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/surveys/popup', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(200);

        $data = json_decode($client->getResponse()->getContent(), true);

        // API Platform omits null properties in JSON serialization, so the empty
        // SurveyPopupResource (id=null, name=null) serializes as an empty array.
        $this->assertEmpty($data, 'Survey popup should be empty for non-team-member');
    }
}
