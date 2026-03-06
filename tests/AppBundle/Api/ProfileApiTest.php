<?php

namespace Tests\AppBundle\Api;

use Tests\BaseWebTestCase;

class ProfileApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    // --- GET /api/me ---

    public function testGetProfileRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/me', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testGetProfileReturnsAuthenticatedUser(): void
    {
        $token = $this->getJwtToken('admin', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/me', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $profile = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('id', $profile);
        $this->assertArrayHasKey('firstName', $profile);
        $this->assertArrayHasKey('lastName', $profile);
        $this->assertArrayHasKey('userName', $profile);
        $this->assertArrayHasKey('email', $profile);
        $this->assertArrayHasKey('phone', $profile);
        $this->assertArrayHasKey('gender', $profile);

        $this->assertEquals('admin', $profile['userName']);
        $this->assertIsInt($profile['id']);
    }

    public function testGetProfileDoesNotExposeSensitiveFields(): void
    {
        $token = $this->getJwtToken('admin', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/me', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $profile = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayNotHasKey('password', $profile);
        $this->assertArrayNotHasKey('companyEmail', $profile);
        $this->assertArrayNotHasKey('accountNumber', $profile);
        $this->assertArrayNotHasKey('new_user_code', $profile);
        $this->assertArrayNotHasKey('roles', $profile);
        $this->assertArrayNotHasKey('isActive', $profile);
    }

    public function testGetProfileWithDifferentUser(): void
    {
        $token = $this->getJwtToken('assistent', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/me', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $profile = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals('assistent', $profile['userName']);
    }

    // --- PUT /api/me ---

    public function testUpdateProfileRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('PUT', '/api/me', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'firstName' => 'Updated',
            'lastName' => 'Name',
            'email' => 'updated@example.com',
        ]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testUpdateProfileFields(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();

        // First get current profile
        $client->request('GET', '/api/me', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);
        $original = json_decode($client->getResponse()->getContent(), true);

        // Update profile
        $client->request('PUT', '/api/me', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'firstName' => 'UpdatedFirst',
            'lastName' => 'UpdatedLast',
            'email' => 'updated-tm@example.com',
            'phone' => '99887766',
            'gender' => 1,
        ]));

        $this->assertResponseIsSuccessful();
        $updated = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals('UpdatedFirst', $updated['firstName']);
        $this->assertEquals('UpdatedLast', $updated['lastName']);
        $this->assertEquals('updated-tm@example.com', $updated['email']);
        $this->assertEquals('99887766', $updated['phone']);

        // id and userName should not change
        $this->assertEquals($original['id'], $updated['id']);
        $this->assertEquals($original['userName'], $updated['userName']);
    }

    public function testUpdateProfileValidatesEmail(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('PUT', '/api/me', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'firstName' => 'Test',
            'lastName' => 'User',
            'email' => 'not-an-email',
        ]));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testUpdateProfileValidatesRequiredFields(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('PUT', '/api/me', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'firstName' => '',
            'lastName' => '',
            'email' => '',
        ]));

        $this->assertResponseStatusCodeSame(422);
    }
}
