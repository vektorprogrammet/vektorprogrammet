<?php

namespace Tests\AppBundle\Api;

use Tests\BaseWebTestCase;

class AdminInterviewListApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    public function testGetInterviewsRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/admin/interviews', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testGetInterviewsReturnsData(): void
    {
        $token = $this->getJwtToken('admin', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/interviews', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('interviews', $data);
        $this->assertIsArray($data['interviews']);
    }

    public function testGetInterviewsContainsExpectedFields(): void
    {
        $token = $this->getJwtToken('admin', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/interviews', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        // Fixtures have applications with interviews in admission-period-current
        $this->assertNotEmpty($data['interviews'], 'Expected at least one interview');

        $interview = $data['interviews'][0];
        $this->assertArrayHasKey('id', $interview);
        $this->assertArrayHasKey('applicantName', $interview);
        $this->assertArrayHasKey('interviewerName', $interview);
        $this->assertArrayHasKey('status', $interview);
        $this->assertArrayHasKey('interviewed', $interview);
        $this->assertArrayHasKey('scheduled', $interview);
        $this->assertArrayHasKey('coInterviewer', $interview);
        $this->assertIsBool($interview['interviewed']);
    }

    public function testGetInterviewsTeamMemberCanAccess(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/interviews', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('interviews', $data);
        $this->assertIsArray($data['interviews']);
    }

    public function testGetInterviewsNoDuplicates(): void
    {
        $token = $this->getJwtToken('admin', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/interviews', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $ids = array_column($data['interviews'], 'id');
        $this->assertCount(count(array_unique($ids)), $ids, 'Interview list should not contain duplicates');
    }

    public function testGetInterviewsDepartmentNotFound(): void
    {
        $token = $this->getJwtToken('admin', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/interviews?department=99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetInterviewsDeniedForOtherDepartment(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/interviews?department=2', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testGetInterviewsSemesterNotFound(): void
    {
        $token = $this->getJwtToken('admin', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/interviews?semester=99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetInterviewsEmptyWhenNoAdmissionPeriod(): void
    {
        // Department 1 (NTNU) + semester 4 (Vår 2015) has no admission period
        $token = $this->getJwtToken('admin', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/interviews?department=1&semester=4', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('interviews', $data);
        $this->assertEmpty($data['interviews']);
    }
}
