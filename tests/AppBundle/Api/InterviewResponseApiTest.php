<?php

namespace Tests\AppBundle\Api;

use Tests\BaseWebTestCase;

class InterviewResponseApiTest extends BaseWebTestCase
{
    public function testGetInterviewResponse(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/interview-responses/code', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(200);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertNotNull($data['id']);
        $this->assertNotNull($data['scheduled']);
        $this->assertEquals('D1-123', $data['room']);
        $this->assertEquals('Gløshaugen', $data['campus']);
        $this->assertNotNull($data['interviewerName']);
        $this->assertNotNull($data['status']);
        $this->assertEquals('code', $data['responseCode']);
        $this->assertArrayNotHasKey('interviewerPhone', $data);
    }

    public function testGetInterviewResponseNotFound(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/interview-responses/nonexistent', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testAcceptInterview(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/interview-responses/code/accept', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(204);
    }

    public function testAcceptInterviewNotFound(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/interview-responses/invalid/accept', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testAcceptInterviewNotPending(): void
    {
        $client = static::createClient();

        // First accept
        $client->request('POST', '/api/interview-responses/code/accept', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ]);
        $this->assertResponseStatusCodeSame(204);

        // Second accept should fail — no longer PENDING
        $client->request('POST', '/api/interview-responses/code/accept', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ]);
        $this->assertResponseStatusCodeSame(422);
    }

    public function testCancelInterview(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/interview-responses/code/cancel', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'cancelMessage' => 'Cannot make it',
        ]));

        $this->assertResponseStatusCodeSame(204);
    }

    public function testCancelInterviewNotFound(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/interview-responses/nonexistent/cancel', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'cancelMessage' => 'Cannot make it',
        ]));

        $this->assertResponseStatusCodeSame(404);
    }

    public function testCancelInterviewNotPending(): void
    {
        $client = static::createClient();

        // First accept the interview
        $client->request('POST', '/api/interview-responses/code/accept', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ]);
        $this->assertResponseStatusCodeSame(204);

        // Cancel should fail — no longer PENDING
        $client->request('POST', '/api/interview-responses/code/cancel', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'cancelMessage' => 'Cannot make it',
        ]));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testRequestNewTime(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/interview-responses/code/request-new-time', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'newTimeMessage' => 'Can we do Thursday?',
        ]));

        $this->assertResponseStatusCodeSame(204);
    }

    public function testRequestNewTimeRequiresMessage(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/interview-responses/code/request-new-time', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'newTimeMessage' => '',
        ]));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testRequestNewTimeNotFound(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/interview-responses/nonexistent/request-new-time', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'newTimeMessage' => 'Can we do Thursday?',
        ]));

        $this->assertResponseStatusCodeSame(404);
    }

    public function testRequestNewTimeNotPending(): void
    {
        $client = static::createClient();

        // First accept the interview
        $client->request('POST', '/api/interview-responses/code/accept', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ]);
        $this->assertResponseStatusCodeSame(204);

        // Request new time should fail — no longer PENDING
        $client->request('POST', '/api/interview-responses/code/request-new-time', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'newTimeMessage' => 'Can we do Thursday?',
        ]));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testGetStatusAfterAccept(): void
    {
        $client = static::createClient();

        // Accept
        $client->request('POST', '/api/interview-responses/code/accept', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ]);
        $this->assertResponseStatusCodeSame(204);

        // GET should reflect new status
        $client->request('GET', '/api/interview-responses/code', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);
        $this->assertResponseStatusCodeSame(200);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Akseptert', $data['status']);
    }
}
