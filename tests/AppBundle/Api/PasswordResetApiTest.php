<?php

namespace Tests\AppBundle\Api;

use Tests\BaseWebTestCase;

class PasswordResetApiTest extends BaseWebTestCase
{
    public function testRequestPasswordResetWithValidEmail(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/password_resets', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'email' => 'admin@gmail.com',
        ]));

        $this->assertResponseStatusCodeSame(204);
    }

    public function testRequestPasswordResetWithNonExistentEmail(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/password_resets', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'email' => 'nonexistent@example.com',
        ]));

        $this->assertResponseStatusCodeSame(204);
    }

    public function testRequestPasswordResetRejectsCompanyEmail(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/password_resets', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'email' => 'someone@vektorprogrammet.no',
        ]));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testRequestPasswordResetValidatesEmail(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/password_resets', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'email' => 'not-an-email',
        ]));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testExecutePasswordResetWithInvalidCode(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/password_resets/invalidcode123', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'password' => 'newpassword123',
        ]));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testExecutePasswordResetValidatesPasswordLength(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/password_resets/somecode', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'password' => 'short',
        ]));

        $this->assertResponseStatusCodeSame(422);
    }
}
