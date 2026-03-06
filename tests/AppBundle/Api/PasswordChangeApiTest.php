<?php

namespace Tests\AppBundle\Api;

use Tests\BaseWebTestCase;

class PasswordChangeApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    public function testChangePasswordRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('PUT', '/api/me/password', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'newPassword' => 'newpassword123',
        ]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testChangePasswordSucceeds(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('PUT', '/api/me/password', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'newPassword' => 'newpassword123',
        ]));

        $this->assertResponseStatusCodeSame(204);
    }

    public function testChangePasswordRejectsShortPassword(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('PUT', '/api/me/password', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'newPassword' => 'short',
        ]));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testChangePasswordRejectsBlankPassword(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('PUT', '/api/me/password', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'newPassword' => '',
        ]));

        $this->assertResponseStatusCodeSame(422);
    }
}
