<?php

namespace Tests\AppBundle\Api;

use Tests\BaseWebTestCase;

class AdminApplicationListApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    public function testGetApplicationsRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/admin/applications', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testGetApplicationsReturnsNewApplications(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/applications?status=new', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('status', $data);
        $this->assertSame('new', $data['status']);
        $this->assertArrayHasKey('applications', $data);
        $this->assertIsArray($data['applications']);
    }

    public function testGetApplicationsReturnsInterviewedApplications(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/applications?status=interviewed', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('status', $data);
        $this->assertSame('interviewed', $data['status']);
        $this->assertArrayHasKey('applications', $data);
        $this->assertIsArray($data['applications']);

        // The fixture has interviewed applications in admission-period-current (dep-1)
        $this->assertNotEmpty($data['applications']);

        $app = $data['applications'][0];
        $this->assertArrayHasKey('id', $app);
        $this->assertArrayHasKey('userName', $app);
        $this->assertArrayHasKey('userEmail', $app);
        $this->assertArrayHasKey('interviewStatus', $app);
        $this->assertArrayHasKey('interviewScheduled', $app);
        $this->assertArrayHasKey('interviewer', $app);
        $this->assertArrayHasKey('previousParticipation', $app);
    }

    public function testGetApplicationsReturnsAssignedApplications(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/applications?status=assigned', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('status', $data);
        $this->assertSame('assigned', $data['status']);
        $this->assertArrayHasKey('applications', $data);
        $this->assertIsArray($data['applications']);
    }

    public function testGetApplicationsReturnsExistingApplications(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/applications?status=existing', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('status', $data);
        $this->assertSame('existing', $data['status']);
        $this->assertArrayHasKey('applications', $data);
        $this->assertIsArray($data['applications']);
    }

    public function testGetApplicationsDefaultsToNewStatus(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/applications', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertSame('new', $data['status']);
    }

    public function testGetApplicationsWithNonExistentDepartmentReturnsEmpty(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/applications?department=99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('applications', $data);
        $this->assertSame('new', $data['status']);
        $this->assertEmpty($data['applications']);
    }

    public function testGetApplicationsWithNonExistentSemesterReturnsEmpty(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/applications?semester=99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('applications', $data);
        $this->assertSame('new', $data['status']);
        $this->assertEmpty($data['applications']);
    }

    public function testGetApplicationsEmptyWhenNoAdmissionPeriod(): void
    {
        $token = $this->getJwtToken('admin', '1234');

        $client = static::createClient();
        // dep-1 (NTNU) + semester-4 (Vår 2015) has no admission period in fixtures
        $client->request('GET', '/api/admin/applications?department=1&semester=4', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('applications', $data);
        $this->assertEmpty($data['applications']);
    }

    public function testGetApplicationsWithInvalidStatusReturnsEmptyApplications(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/applications?status=invalidstatus', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('status', $data);
        $this->assertSame('invalidstatus', $data['status']);
        $this->assertArrayHasKey('applications', $data);
        $this->assertEmpty($data['applications']);
    }
}
