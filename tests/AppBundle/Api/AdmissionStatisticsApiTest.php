<?php

namespace Tests\AppBundle\Api;

use Tests\BaseWebTestCase;

class AdmissionStatisticsApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    public function testGetAdmissionStatisticsRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/admin/admission-statistics', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testGetAdmissionStatisticsReturnsStats(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/admission-statistics?department=1&semester=1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('applicationCount', $data);
        $this->assertArrayHasKey('maleApplications', $data);
        $this->assertArrayHasKey('femaleApplications', $data);
        $this->assertArrayHasKey('assistantCount', $data);
        $this->assertArrayHasKey('maleAssistants', $data);
        $this->assertArrayHasKey('femaleAssistants', $data);
        $this->assertArrayHasKey('departmentName', $data);
        $this->assertArrayHasKey('semesterName', $data);

        $this->assertIsInt($data['applicationCount']);
        $this->assertIsInt($data['maleApplications']);
        $this->assertIsInt($data['femaleApplications']);
        $this->assertIsInt($data['assistantCount']);
        $this->assertIsInt($data['maleAssistants']);
        $this->assertIsInt($data['femaleAssistants']);
        $this->assertIsString($data['departmentName']);
        $this->assertIsString($data['semesterName']);
    }

    public function testGetAdmissionStatisticsDefaultsToUserDepartment(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/admission-statistics', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('departmentName', $data);
        $this->assertNotEmpty($data['departmentName']);
        $this->assertArrayHasKey('semesterName', $data);
        $this->assertNotEmpty($data['semesterName']);
    }

    public function testGetAdmissionStatisticsNonexistentDepartmentReturnsEmptyName(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/admission-statistics?department=99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertSame('', $data['departmentName']);
        $this->assertSame(0, $data['applicationCount']);
        $this->assertSame(0, $data['assistantCount']);
    }

    public function testGetAdmissionStatisticsNonexistentSemesterReturnsEmptyName(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/admission-statistics?semester=99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertSame('', $data['semesterName']);
        $this->assertSame(0, $data['applicationCount']);
        $this->assertSame(0, $data['assistantCount']);
    }

    public function testGetAdmissionStatisticsIncludesGenderCounts(): void
    {
        $token = $this->getJwtToken('admin', '1234');

        $client = static::createClient();
        // Default params resolve to user's department + current semester
        $client->request('GET', '/api/admin/admission-statistics', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        // Gender count fields should be present and be integers
        $this->assertArrayHasKey('maleAssistants', $data);
        $this->assertArrayHasKey('femaleAssistants', $data);
        $this->assertIsInt($data['maleAssistants']);
        $this->assertIsInt($data['femaleAssistants']);
    }

    public function testGetAdmissionStatisticsNullDepartmentAndSemesterSkipsAdmissionPeriod(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/admission-statistics?department=99999&semester=99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertSame('', $data['departmentName']);
        $this->assertSame('', $data['semesterName']);
        $this->assertSame(0, $data['applicationCount']);
        $this->assertSame(0, $data['maleApplications']);
        $this->assertSame(0, $data['femaleApplications']);
        $this->assertSame(0, $data['assistantCount']);
        $this->assertSame(0, $data['maleAssistants']);
        $this->assertSame(0, $data['femaleAssistants']);
    }
}
