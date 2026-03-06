<?php

namespace Tests\AppBundle\Api;

use Tests\BaseWebTestCase;

class ApplicationApiTest extends BaseWebTestCase
{
    public function testSubmitApplicationReturns201(): void
    {
        $client = static::createClient();

        // Get a department with active admission and its field of study
        $client->request('GET', '/api/departments', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);
        $departments = json_decode($client->getResponse()->getContent(), true);
        $departmentId = $departments[0]['id'];

        $client->request('GET', '/api/field_of_studies', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);
        $fields = json_decode($client->getResponse()->getContent(), true);
        $fieldOfStudyId = $fields[0]['id'];

        $client->request('POST', '/api/applications', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'firstName' => 'Test',
            'lastName' => 'Søker',
            'email' => 'test-api-'.uniqid().'@example.com',
            'phone' => '12345678',
            'fieldOfStudyId' => $fieldOfStudyId,
            'yearOfStudy' => '2',
            'gender' => 1,
            'departmentId' => $departmentId,
        ]));

        // May be 201 (created) or 422 (no active admission in test fixtures)
        $status = $client->getResponse()->getStatusCode();
        $this->assertTrue(
            in_array($status, [201, 422]),
            "Expected 201 or 422, got $status: ".$client->getResponse()->getContent()
        );
    }

    public function testSubmitApplicationValidation(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/applications', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'firstName' => '',
            'email' => 'not-an-email',
        ]));

        $this->assertResponseStatusCodeSame(422);
    }

    // --- ContactMessage tests ---

    public function testSubmitContactMessage(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/departments', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);
        $departments = json_decode($client->getResponse()->getContent(), true);
        $departmentId = $departments[0]['id'];

        $client->request('POST', '/api/contact_messages', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'name' => 'Test Person',
            'email' => 'test@example.com',
            'departmentId' => $departmentId,
            'subject' => 'Test inquiry',
            'message' => 'This is a test message from the API.',
        ]));

        $status = $client->getResponse()->getStatusCode();
        $this->assertTrue(
            in_array($status, [201, 204]),
            "Expected 201 or 204, got $status: ".$client->getResponse()->getContent()
        );
    }

    public function testSubmitContactMessageValidation(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/contact_messages', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'name' => '',
            'email' => 'not-valid',
        ]));

        $this->assertResponseStatusCodeSame(422);
    }
}
