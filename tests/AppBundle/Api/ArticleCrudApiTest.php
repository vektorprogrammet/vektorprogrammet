<?php

namespace Tests\AppBundle\Api;

use Tests\BaseWebTestCase;

class ArticleCrudApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    public function testCreateArticleRequiresAdmin(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/articles', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['title' => 'Test', 'article' => 'Content']));
        $this->assertResponseStatusCodeSame(401);
    }

    public function testCreateArticleAsNonAdminForbidden(): void
    {
        $token = $this->getJwtToken('assistent', '1234');
        $client = static::createClient();
        $client->request('POST', '/api/articles', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ], json_encode(['title' => 'Test', 'article' => 'Content']));
        $this->assertResponseStatusCodeSame(403);
    }

    public function testCreateArticle(): void
    {
        $token = $this->getJwtToken('admin', '1234');
        $client = static::createClient();
        $client->request('POST', '/api/articles', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ], json_encode([
            'title' => 'API Test Article',
            'article' => '<p>Test content from API</p>',
            'published' => true,
            'sticky' => false,
        ]));
        $this->assertResponseStatusCodeSame(201);
        $article = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('API Test Article', $article['title']);
        $this->assertArrayHasKey('slug', $article);
        $this->assertNotEmpty($article['slug']);
    }

    public function testUpdateArticle(): void
    {
        $token = $this->getJwtToken('admin', '1234');
        $client = static::createClient();

        // Get an existing article
        $client->request('GET', '/api/articles', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);
        $articles = json_decode($client->getResponse()->getContent(), true);
        $this->assertNotEmpty($articles);
        $id = $articles[0]['id'];

        // Update it
        $client->request('PUT', "/api/articles/$id", [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ], json_encode([
            'title' => 'Updated Title',
            'article' => '<p>Updated content</p>',
        ]));
        $this->assertResponseIsSuccessful();
        $updated = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Updated Title', $updated['title']);
    }

    public function testDeleteArticle(): void
    {
        $token = $this->getJwtToken('admin', '1234');
        $client = static::createClient();

        // Create one to delete
        $client->request('POST', '/api/articles', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ], json_encode([
            'title' => 'To Be Deleted',
            'article' => '<p>Delete me</p>',
            'published' => false,
        ]));
        $this->assertResponseStatusCodeSame(201);
        $article = json_decode($client->getResponse()->getContent(), true);
        $id = $article['id'];

        // Delete it
        $client->request('DELETE', "/api/articles/$id", [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);
        $this->assertResponseStatusCodeSame(204);
    }
}
