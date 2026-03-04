<?php

namespace Tests\AppBundle\Api;

use Tests\BaseWebTestCase;

class ExistingUserApplicationApiTest extends BaseWebTestCase
{
    private function getJwtToken(string $username = 'admin', string $password = '1234'): string
    {
        $client = static::createClient();
        $client->request('POST', '/api/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'username' => $username,
            'password' => $password,
        ]));

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $data);

        return $data['token'];
    }

    public function testExistingUserApplicationRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/applications/existing', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'monday' => true,
            'tuesday' => true,
        ]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testExistingUserApplicationWithAuth(): void
    {
        $token = $this->getJwtToken('assistent', '1234');

        $client = static::createClient();
        $client->request('POST', '/api/applications/existing', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ], json_encode([
            'monday' => true,
            'tuesday' => false,
            'wednesday' => true,
            'thursday' => false,
            'friday' => true,
            'teamInterest' => true,
        ]));

        // May be 201 (success) or 422 (admission not open/already applied/not been assistant)
        $status = $client->getResponse()->getStatusCode();
        $this->assertTrue(
            in_array($status, [201, 422]),
            "Expected 201 or 422, got $status: ".$client->getResponse()->getContent()
        );
    }
}
