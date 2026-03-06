<?php

namespace Tests\AppBundle\Api;

trait JwtAuthTrait
{
    private function getJwtToken(string $username = 'admin', string $password = '1234'): string
    {
        $client = static::createClient();
        $client->request('POST', '/api/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['username' => $username, 'password' => $password]));

        $this->assertResponseIsSuccessful();

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $data);

        return $data['token'];
    }
}
