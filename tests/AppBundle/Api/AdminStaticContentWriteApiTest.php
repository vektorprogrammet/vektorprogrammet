<?php

namespace Tests\AppBundle\Api;

use App\Content\Infrastructure\Entity\StaticContent;
use Doctrine\ORM\EntityManagerInterface;
use Tests\BaseWebTestCase;

class AdminStaticContentWriteApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    // --- PUT /api/admin/static-content/{id} (edit) ---

    public function testEditRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('PUT', '/api/admin/static-content/1', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['html' => '<p>Test</p>']));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testEditForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');
        $client = static::createClient();
        $client->request('PUT', '/api/admin/static-content/1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['html' => '<p>Test</p>']));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testEditForbiddenForTeamMember(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();
        $client->request('PUT', '/api/admin/static-content/1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['html' => '<p>Test</p>']));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testEditSuccess(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $content = $em->getRepository(StaticContent::class)->findAll()[0];

        $payload = [
            'html' => '<p>Updated content via API</p>',
        ];

        $client->request('PUT', '/api/admin/static-content/'.$content->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(200);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame($content->getId(), $data['id']);
        $this->assertSame('<p>Updated content via API</p>', $data['html']);
    }

    public function testEditNotFoundReturns404(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        $client->request('PUT', '/api/admin/static-content/99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['html' => '<p>Whatever</p>']));

        $this->assertResponseStatusCodeSame(404);
    }

    public function testEditAdminCanAlsoAccess(): void
    {
        $token = $this->getJwtToken('admin', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $content = $em->getRepository(StaticContent::class)->findAll()[0];

        $payload = [
            'html' => '<p>Admin updated content</p>',
        ];

        $client->request('PUT', '/api/admin/static-content/'.$content->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(200);
    }
}
