<?php

namespace Tests\AppBundle\Api;

use Tests\BaseWebTestCase;

class ContentApiTest extends BaseWebTestCase
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

    // --- Article tests (preserved from ArticleApiTest) ---

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

    // --- ChangeLogItem tests ---

    public function testGetChangeLogItemCollection(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/change_log_items', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($response);
    }

    public function testGetChangeLogItemCollectionRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/change_log_items', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    // --- Feedback tests ---

    public function testGetFeedbackCollection(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/feedback', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($response);
    }

    public function testGetFeedbackCollectionRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/feedback', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    // --- Sponsor tests ---

    public function testGetSponsorCollection(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/sponsors', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($response);
    }

    public function testGetSponsorCollectionRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/sponsors', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    // --- StaticContent tests ---

    public function testGetStaticContentCollection(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/static_contents', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($response);
    }

    public function testGetStaticContentCollectionRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/static_contents', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }
}
