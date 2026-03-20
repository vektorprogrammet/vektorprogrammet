<?php

namespace Tests\AppBundle\Api;

use App\Organization\Infrastructure\Entity\Department;
use Tests\BaseWebTestCase;

class ContactMessageApiTest extends BaseWebTestCase
{
    private function getValidPayload(int $departmentId): array
    {
        return [
            'name' => 'Ola Nordmann',
            'email' => 'ola@example.com',
            'departmentId' => $departmentId,
            'subject' => 'Test henvendelse',
            'message' => 'Dette er en testmelding fra kontaktskjemaet.',
        ];
    }

    private function postContactMessage(array $payload): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/contact_messages', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));
    }

    private function findDepartmentId(): int
    {
        $client = static::createClient();
        $em = self::getContainer()->get('doctrine')->getManager();
        $dept = $em->getRepository(Department::class)->findAll()[0];
        $this->assertNotNull($dept, 'Fixtures must contain at least one department');

        return $dept->getId();
    }

    public function testCreateContactMessageSuccess(): void
    {
        $deptId = $this->findDepartmentId();

        $this->postContactMessage($this->getValidPayload($deptId));

        $this->assertResponseStatusCodeSame(201);
    }

    public function testCreateContactMessageValidationRejectsBlankName(): void
    {
        $deptId = $this->findDepartmentId();
        $payload = $this->getValidPayload($deptId);
        $payload['name'] = '';

        $this->postContactMessage($payload);

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateContactMessageValidationRejectsBlankEmail(): void
    {
        $deptId = $this->findDepartmentId();
        $payload = $this->getValidPayload($deptId);
        $payload['email'] = '';

        $this->postContactMessage($payload);

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateContactMessageValidationRejectsInvalidEmail(): void
    {
        $deptId = $this->findDepartmentId();
        $payload = $this->getValidPayload($deptId);
        $payload['email'] = 'not-an-email';

        $this->postContactMessage($payload);

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateContactMessageValidationRejectsBlankSubject(): void
    {
        $deptId = $this->findDepartmentId();
        $payload = $this->getValidPayload($deptId);
        $payload['subject'] = '';

        $this->postContactMessage($payload);

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateContactMessageValidationRejectsBlankBody(): void
    {
        $deptId = $this->findDepartmentId();
        $payload = $this->getValidPayload($deptId);
        $payload['message'] = '';

        $this->postContactMessage($payload);

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateContactMessageValidationRejectsNullDepartment(): void
    {
        $payload = [
            'name' => 'Ola Nordmann',
            'email' => 'ola@example.com',
            'departmentId' => null,
            'subject' => 'Test henvendelse',
            'message' => 'En melding.',
        ];

        $this->postContactMessage($payload);

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateContactMessageRejectsNonExistentDepartment(): void
    {
        $payload = $this->getValidPayload(999999);

        $this->postContactMessage($payload);

        $this->assertResponseStatusCodeSame(422);
    }
}
