<?php

namespace Tests\AppBundle\Api;

use Tests\BaseWebTestCase;

class UserProfileApiTest extends BaseWebTestCase
{
    public function testGetPublicUserProfile(): void
    {
        $client = static::createClient();

        // user-1 (Petter Johansen, ID 1) has team memberships in fixtures
        $client->request('GET', '/api/users/1', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);
        $this->assertResponseIsSuccessful();
        $profile = json_decode($client->getResponse()->getContent(), true);

        // Expected public fields
        $this->assertArrayHasKey('id', $profile);
        $this->assertArrayHasKey('firstName', $profile);
        $this->assertArrayHasKey('lastName', $profile);
        $this->assertArrayHasKey('picturePath', $profile);

        $this->assertEquals(1, $profile['id']);
        $this->assertEquals('Petter', $profile['firstName']);
        $this->assertEquals('Johansen', $profile['lastName']);

        // Sensitive fields must NOT be exposed
        $this->assertArrayNotHasKey('password', $profile);
        $this->assertArrayNotHasKey('email', $profile);
        $this->assertArrayNotHasKey('phone', $profile);
        $this->assertArrayNotHasKey('companyEmail', $profile);
        $this->assertArrayNotHasKey('accountNumber', $profile);
        $this->assertArrayNotHasKey('new_user_code', $profile);
        $this->assertArrayNotHasKey('roles', $profile);
    }

    public function testGetNonExistentUserReturns404(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/users/99999', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);
        $this->assertResponseStatusCodeSame(404);
    }
}
