<?php

namespace Tests\AppBundle\Api;

use Tests\BaseWebTestCase;

class TeamInterestApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    public function testGetTeamInterestRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/admin/team-interest', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testGetTeamInterestReturnsData(): void
    {
        $token = $this->getJwtToken('admin', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/team-interest', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('applicants', $data);
        $this->assertArrayHasKey('teams', $data);
        $this->assertIsArray($data['applicants']);
        $this->assertIsArray($data['teams']);
    }

    public function testGetTeamInterestApplicantsHaveExpectedFields(): void
    {
        $token = $this->getJwtToken('admin', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/team-interest', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        // Fixtures have applications with teamInterest=true in admission-period-current
        $this->assertNotEmpty($data['applicants'], 'Expected at least one applicant with team interest');

        $applicant = $data['applicants'][0];
        $this->assertArrayHasKey('id', $applicant);
        $this->assertArrayHasKey('name', $applicant);
        $this->assertArrayHasKey('teams', $applicant);
        $this->assertIsArray($applicant['teams']);
    }

    public function testGetTeamInterestTeamsHaveExpectedFields(): void
    {
        $token = $this->getJwtToken('admin', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/team-interest', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($data['teams'], 'Expected at least one team with interest');

        $team = $data['teams'][0];
        $this->assertArrayHasKey('id', $team);
        $this->assertArrayHasKey('name', $team);
    }

    public function testGetTeamInterestTeamMemberCanAccessOwnDepartment(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/team-interest', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
    }

    public function testGetTeamInterestDepartmentNotFound(): void
    {
        $token = $this->getJwtToken('admin', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/team-interest?department=99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetTeamInterestDeniedForOtherDepartment(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/team-interest?department=2', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testGetTeamInterestSemesterNotFound(): void
    {
        $token = $this->getJwtToken('admin', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/team-interest?semester=99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetTeamInterestEmptyWhenNoAdmissionPeriod(): void
    {
        // Department 1 (NTNU) + semester 4 (Vår 2015) has no admission period
        $token = $this->getJwtToken('admin', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/team-interest?department=1&semester=4', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('applicants', $data);
        $this->assertArrayHasKey('teams', $data);
        $this->assertEmpty($data['applicants']);
        $this->assertEmpty($data['teams']);
    }
}
