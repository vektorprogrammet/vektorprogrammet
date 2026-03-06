<?php

namespace Tests\AppBundle\Api;

use App\Entity\FieldOfStudy;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Tests\BaseWebTestCase;

class AdminFieldOfStudyWriteApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    // --- POST /api/admin/field-of-studies ---

    public function testCreateFieldOfStudyRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/admin/field-of-studies', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => 'Test FOS', 'shortName' => 'TFOS']));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testCreateFieldOfStudyForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');
        $client = static::createClient();
        $client->request('POST', '/api/admin/field-of-studies', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => 'Test FOS', 'shortName' => 'TFOS']));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testCreateFieldOfStudySuccess(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        $payload = [
            'name' => 'Ny Linje',
            'shortName' => 'NL',
        ];

        $client->request('POST', '/api/admin/field-of-studies', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertIsInt($data['id']);

        // Verify the department was set to the user's department (dep-1 NTNU)
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $fos = $em->getRepository(FieldOfStudy::class)->find($data['id']);
        $this->assertNotNull($fos);

        $user = $em->getRepository(User::class)->findOneBy(['user_name' => 'teamleader']);
        $this->assertSame($user->getDepartment()->getId(), $fos->getDepartment()->getId());
    }

    public function testCreateFieldOfStudyValidationRejectsBlankFields(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        $payload = [
            'name' => '',
            'shortName' => '',
        ];

        $client->request('POST', '/api/admin/field-of-studies', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    // --- PUT /api/admin/field-of-studies/{id} ---

    public function testUpdateFieldOfStudyRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('PUT', '/api/admin/field-of-studies/1', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => 'Updated', 'shortName' => 'UPD']));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testUpdateFieldOfStudyForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');
        $client = static::createClient();
        $client->request('PUT', '/api/admin/field-of-studies/1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => 'Updated', 'shortName' => 'UPD']));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testUpdateFieldOfStudySuccess(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        // teamleader is in dep-1 (NTNU), fos-1 'BIT' is also in dep-1
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $fos = $em->getRepository(FieldOfStudy::class)->findOneBy(['shortName' => 'BIT']);

        $payload = [
            'name' => 'Updated BIT',
            'shortName' => 'BIT2',
        ];

        $client->request('PUT', '/api/admin/field-of-studies/'.$fos->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(200);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
    }

    public function testUpdateFieldOfStudyNotFoundReturns404(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        $client->request('PUT', '/api/admin/field-of-studies/99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => 'No FOS', 'shortName' => 'XX']));

        $this->assertResponseStatusCodeSame(404);
    }

    public function testUpdateFieldOfStudyFromOtherDepartmentReturns403(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        // teamleader is in dep-1 (NTNU), fos-3 'BITA' is in dep-2 (UiB)
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $fos = $em->getRepository(FieldOfStudy::class)->findOneBy(['shortName' => 'BITA']);

        $payload = [
            'name' => 'Attempt Cross-Department Edit',
            'shortName' => 'HACK',
        ];

        $client->request('PUT', '/api/admin/field-of-studies/'.$fos->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testUpdateFieldOfStudyValidationRejectsBlankName(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $fos = $em->getRepository(FieldOfStudy::class)->findOneBy(['shortName' => 'BIT']);

        $payload = [
            'name' => '',
            'shortName' => '',
        ];

        $client->request('PUT', '/api/admin/field-of-studies/'.$fos->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }
}
