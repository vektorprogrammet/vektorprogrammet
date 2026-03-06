<?php

namespace Tests\AppBundle\Api;

use App\Entity\ChangeLogItem;
use Doctrine\ORM\EntityManagerInterface;
use Tests\BaseWebTestCase;

class AdminChangelogWriteApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    // --- POST /api/admin/changelogs (create) ---

    public function testCreateRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/admin/changelogs', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['title' => 'Test']));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testCreateForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');
        $client = static::createClient();
        $client->request('POST', '/api/admin/changelogs', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['title' => 'Test']));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testCreateSuccess(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $payload = [
            'title' => 'New Changelog Entry',
            'description' => 'A new feature was added',
            'date' => '2026-03-05T12:00:00+00:00',
            'githubLink' => 'https://github.com/example/repo/pull/1',
        ];

        $client->request('POST', '/api/admin/changelogs', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertIsInt($data['id']);
    }

    public function testCreateValidationRejectsBlankTitle(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $payload = [
            'title' => '',
            'description' => 'desc',
            'date' => '2026-03-05T12:00:00+00:00',
            'githubLink' => 'https://github.com/example',
        ];

        $client->request('POST', '/api/admin/changelogs', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateValidationRejectsTitleTooLong(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $payload = [
            'title' => str_repeat('A', 41),
            'description' => 'desc',
            'date' => '2026-03-05T12:00:00+00:00',
            'githubLink' => 'https://github.com/example',
        ];

        $client->request('POST', '/api/admin/changelogs', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    // --- PUT /api/admin/changelogs/{id} (edit) ---

    public function testEditRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('PUT', '/api/admin/changelogs/1', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['title' => 'Updated']));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testEditForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');
        $client = static::createClient();
        $client->request('PUT', '/api/admin/changelogs/1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['title' => 'Updated']));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testEditSuccess(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $changelog = $em->getRepository(ChangeLogItem::class)->findAll()[0];

        $payload = [
            'title' => 'Updated Title',
            'description' => 'Updated description',
            'date' => '2026-04-01T10:00:00+00:00',
            'githubLink' => 'https://github.com/example/updated',
        ];

        $client->request('PUT', '/api/admin/changelogs/'.$changelog->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(200);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('Updated Title', $data['title']);
    }

    public function testEditNotFoundReturns404(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $client->request('PUT', '/api/admin/changelogs/99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['title' => 'Whatever']));

        $this->assertResponseStatusCodeSame(404);
    }

    public function testEditValidationRejectsBlankTitle(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $changelog = $em->getRepository(ChangeLogItem::class)->findAll()[0];

        $client->request('PUT', '/api/admin/changelogs/'.$changelog->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['title' => '']));

        $this->assertResponseStatusCodeSame(422);
    }

    // --- DELETE /api/admin/changelogs/{id} ---

    public function testDeleteRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '/api/admin/changelogs/1', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testDeleteForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');
        $client = static::createClient();
        $client->request('DELETE', '/api/admin/changelogs/1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testDeleteSuccess(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);

        $item = new ChangeLogItem();
        $item->setTitle('To Delete');
        $item->setDescription('Will be deleted');
        $item->setGithubLink('https://github.com/example/delete');
        $item->setDate(new \DateTime());
        $em->persist($item);
        $em->flush();
        $itemId = $item->getId();

        $client->request('DELETE', '/api/admin/changelogs/'.$itemId, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(204);

        $em->clear();
        $deleted = $em->getRepository(ChangeLogItem::class)->find($itemId);
        $this->assertNull($deleted);
    }

    public function testDeleteNotFoundReturns404(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $client->request('DELETE', '/api/admin/changelogs/99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(404);
    }
}
