<?php

namespace Tests\AppBundle\Api;

use Tests\BaseWebTestCase;

class ArticleApiTest extends BaseWebTestCase
{
    private ?string $token = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->token = $this->getJwtToken();
    }

    private function getJwtToken(): string
    {
        $client = static::createClient();
        $client->request('POST', '/api/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'username' => 'admin',
            'password' => '1234',
        ]));

        $response = $client->getResponse();
        $this->assertResponseIsSuccessful();

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('token', $data);

        return $data['token'];
    }

    public function testGetArticleCollection(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/articles', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($response);
    }

    public function testGetArticleCollectionRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/articles', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }
}
