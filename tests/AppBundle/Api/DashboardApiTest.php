<?php

namespace Tests\AppBundle\Api;

use Tests\BaseWebTestCase;

class DashboardApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    public function testGetDashboardRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/me/dashboard', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testGetDashboardReturnsUserData(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/me/dashboard', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('firstName', $data);
        $this->assertArrayHasKey('lastName', $data);
        $this->assertArrayHasKey('email', $data);
        $this->assertArrayHasKey('activeApplication', $data);
        $this->assertArrayHasKey('activeAssistantHistories', $data);
        $this->assertIsArray($data['activeAssistantHistories']);
    }

    public function testGetDashboardDoesNotExposeSensitiveFields(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/me/dashboard', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayNotHasKey('password', $data);
        $this->assertArrayNotHasKey('roles', $data);
        $this->assertArrayNotHasKey('accountNumber', $data);
    }
}
