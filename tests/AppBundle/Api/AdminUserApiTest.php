<?php

namespace Tests\AppBundle\Api;

use Tests\BaseWebTestCase;

class AdminUserApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    public function testGetUsersRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/admin/users', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testGetUsersAllowedForTeamMember(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/users', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
    }

    public function testGetUsersReturnsDataForTeamLeader(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/users', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('activeUsers', $data);
        $this->assertArrayHasKey('inactiveUsers', $data);
        $this->assertArrayHasKey('departmentName', $data);
        $this->assertIsArray($data['activeUsers']);
        $this->assertIsArray($data['inactiveUsers']);
        $this->assertNotEmpty($data['departmentName']);

        $this->assertNotEmpty($data['activeUsers']);

        $firstUser = $data['activeUsers'][0];
        $this->assertArrayHasKey('id', $firstUser);
        $this->assertArrayHasKey('firstName', $firstUser);
        $this->assertArrayHasKey('lastName', $firstUser);
        $this->assertArrayHasKey('email', $firstUser);
        $this->assertArrayHasKey('role', $firstUser);
    }

    public function testGetUsersWithNonExistentDepartmentReturnsEmptyResource(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/users?department=99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('activeUsers', $data);
        $this->assertArrayHasKey('inactiveUsers', $data);
        $this->assertArrayHasKey('departmentName', $data);
        $this->assertEmpty($data['activeUsers']);
        $this->assertEmpty($data['inactiveUsers']);
        $this->assertSame('', $data['departmentName']);
    }
}
