<?php

namespace Tests\AppBundle\Api;

use Tests\BaseWebTestCase;

class AdmissionSubscriberApiTest extends BaseWebTestCase
{
    public function testSubscribeReturns201(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/admission_subscribers', [], [], [
            'HTTP_ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => 'test-subscriber@example.com',
            'departmentId' => 1,
            'infoMeeting' => false,
        ]));
        $this->assertResponseStatusCodeSame(201);
    }

    public function testDuplicateSubscribeIsIdempotent(): void
    {
        $client = static::createClient();
        $payload = json_encode([
            'email' => 'duplicate-test@example.com',
            'departmentId' => 1,
            'infoMeeting' => false,
        ]);
        $headers = [
            'HTTP_ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
        ];

        $client->request('POST', '/api/admission_subscribers', [], [], $headers, $payload);
        $this->assertResponseStatusCodeSame(201);

        // Second request should also succeed (idempotent)
        $client->request('POST', '/api/admission_subscribers', [], [], $headers, $payload);
        $this->assertResponseStatusCodeSame(201);
    }

    public function testSubscribeWithInvalidDepartmentReturns422(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/admission_subscribers', [], [], [
            'HTTP_ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => 'test@example.com',
            'departmentId' => 99999,
            'infoMeeting' => false,
        ]));
        $this->assertResponseStatusCodeSame(422);
    }

    public function testSubscribeWithInvalidEmailReturns422(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/admission_subscribers', [], [], [
            'HTTP_ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => 'not-an-email',
            'departmentId' => 1,
            'infoMeeting' => false,
        ]));
        $this->assertResponseStatusCodeSame(422);
    }
}
