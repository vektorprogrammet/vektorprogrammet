<?php

namespace Tests\AppBundle\Api;

use Tests\BaseWebTestCase;

class AdminSchedulingApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    // --- Assistants endpoint ---

    public function testAssistantsRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/admin/scheduling/assistants', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testAssistantsRequiresTeamMemberRole(): void
    {
        $token = $this->getJwtToken('assistent', '1234');
        $client = static::createClient();
        $client->request('GET', '/api/admin/scheduling/assistants', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testAssistantsReturnsArrayForTeamLeader(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();
        $client->request('GET', '/api/admin/scheduling/assistants', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($data);

        // Fixtures have allocatable applications for dep-1 + current semester
        // (application3 with interviewed=true, application4 with interviewed=true,
        //  100 scheduling applications with interviewed=true, application0 with previousParticipation=true)
        $this->assertNotEmpty($data, 'Should have allocatable assistants from fixtures');

        // Check structure of first item -- always-present fields
        $first = $data[0];
        $this->assertArrayHasKey('name', $first);
        $this->assertArrayHasKey('email', $first);
        $this->assertArrayHasKey('doublePosition', $first);
        $this->assertArrayHasKey('availability', $first);
        $this->assertArrayHasKey('score', $first);
        $this->assertArrayHasKey('suitability', $first);
        $this->assertArrayHasKey('previousParticipation', $first);

        // preferredGroup and language may be null (omitted by API Platform)
        // Verify they appear on at least one item in the array
        $hasPreferredGroup = false;
        $hasLanguage = false;
        foreach ($data as $item) {
            if (array_key_exists('preferredGroup', $item)) {
                $hasPreferredGroup = true;
            }
            if (array_key_exists('language', $item)) {
                $hasLanguage = true;
            }
        }
        $this->assertTrue($hasPreferredGroup, 'At least one assistant should have preferredGroup');
        $this->assertTrue($hasLanguage, 'At least one assistant should have language');

        // Availability should have day keys
        $availability = $first['availability'];
        $this->assertArrayHasKey('Monday', $availability);
        $this->assertArrayHasKey('Tuesday', $availability);
        $this->assertArrayHasKey('Wednesday', $availability);
        $this->assertArrayHasKey('Thursday', $availability);
        $this->assertArrayHasKey('Friday', $availability);
    }

    // --- Schools endpoint ---

    public function testSchoolsRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/admin/scheduling/schools', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testSchoolsRequiresTeamMemberRole(): void
    {
        $token = $this->getJwtToken('assistent', '1234');
        $client = static::createClient();
        $client->request('GET', '/api/admin/scheduling/schools', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testSchoolsReturnsArrayForTeamLeader(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();
        $client->request('GET', '/api/admin/scheduling/schools', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($data);

        // Fixtures have 10 school capacities for dep-1 + current semester
        $this->assertNotEmpty($data, 'Should have school data from fixtures');

        // Check structure of first item
        $first = $data[0];
        $this->assertArrayHasKey('id', $first);
        $this->assertArrayHasKey('name', $first);
        $this->assertArrayHasKey('capacity', $first);

        // Capacity should have groups 1 and 2
        $capacity = $first['capacity'];
        $this->assertArrayHasKey(1, $capacity);
        $this->assertArrayHasKey(2, $capacity);

        // Each group should have day keys
        $group1 = $capacity[1];
        $this->assertArrayHasKey('Monday', $group1);
        $this->assertArrayHasKey('Tuesday', $group1);
        $this->assertArrayHasKey('Wednesday', $group1);
        $this->assertArrayHasKey('Thursday', $group1);
        $this->assertArrayHasKey('Friday', $group1);
    }
}
