<?php

namespace Tests\AppBundle\Api;

use Tests\BaseWebTestCase;

class TeamApplicationApiTest extends BaseWebTestCase
{
    public function testSubmitTeamApplication(): void
    {
        $client = static::createClient();

        // Get a team ID from the API
        $client->request('GET', '/api/teams', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);
        $this->assertResponseIsSuccessful();
        $teams = json_decode($client->getResponse()->getContent(), true);
        $this->assertNotEmpty($teams);
        $teamId = $teams[0]['id'];

        $client->request('POST', '/api/team_applications', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'teamId' => $teamId,
            'name' => 'Test Applicant',
            'email' => 'applicant@example.com',
            'phone' => '12345678',
            'fieldOfStudy' => 'Informatikk',
            'yearOfStudy' => '2. klasse',
            'motivationText' => 'I want to join because it is a great opportunity.',
            'biography' => 'I am a student at NTNU studying computer science.',
        ]));

        $this->assertResponseStatusCodeSame(201);
    }

    public function testSubmitTeamApplicationValidation(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/team_applications', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'teamId' => null,
            'name' => '',
            'email' => 'invalid',
        ]));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testSubmitTeamApplicationInvalidTeam(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/team_applications', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'teamId' => 99999,
            'name' => 'Test',
            'email' => 'test@example.com',
            'phone' => '12345678',
            'fieldOfStudy' => 'Test',
            'yearOfStudy' => '1. klasse',
            'motivationText' => 'Test motivation text for the team application.',
            'biography' => 'Test biography for the team application.',
        ]));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testSubmitTeamApplicationInvalidYearOfStudy(): void
    {
        $client = static::createClient();

        // Get a team ID from the API
        $client->request('GET', '/api/teams', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);
        $teams = json_decode($client->getResponse()->getContent(), true);
        $teamId = $teams[0]['id'];

        $client->request('POST', '/api/team_applications', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'teamId' => $teamId,
            'name' => 'Test Applicant',
            'email' => 'test@example.com',
            'phone' => '12345678',
            'fieldOfStudy' => 'Informatikk',
            'yearOfStudy' => '6. klasse',
            'motivationText' => 'Test motivation',
            'biography' => 'Test biography',
        ]));

        $this->assertResponseStatusCodeSame(422);
    }
}
