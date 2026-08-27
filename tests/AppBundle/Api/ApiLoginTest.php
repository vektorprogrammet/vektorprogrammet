<?php

namespace Tests\AppBundle\Api;

use Tests\BaseWebTestCase;

class ApiLoginTest extends BaseWebTestCase
{
    public function testValidCredentialsReturnJwt(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['username' => 'admin', 'password' => '1234']));

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('token', $data);
        $this->assertIsString($data['token']);
        $this->assertNotSame('', $data['token']);
    }

    public function testInvalidCredentialsUseJwtFailureResponse(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['username' => 'admin', 'password' => 'incorrect']));

        $this->assertResponseStatusCodeSame(401);
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertIsArray($data);
        $this->assertSame(401, $data['code']);
        $this->assertIsString($data['message']);
        $this->assertNotSame('', $data['message']);
    }
}
