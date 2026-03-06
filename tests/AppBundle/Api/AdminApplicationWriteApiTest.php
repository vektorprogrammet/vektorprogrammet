<?php

namespace Tests\AppBundle\Api;

use App\Entity\Application;
use Doctrine\ORM\EntityManagerInterface;
use Tests\BaseWebTestCase;

class AdminApplicationWriteApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    // --- Endpoint 8: POST /api/admin/applications (create) ---

    public function testCreateApplicationRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/admin/applications', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['firstName' => 'Test', 'lastName' => 'User', 'email' => 'new@example.com']));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testCreateApplicationForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');
        $client = static::createClient();
        $client->request('POST', '/api/admin/applications', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['firstName' => 'Test', 'lastName' => 'User', 'email' => 'new@example.com']));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testCreateApplicationSuccess(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $fos = $em->getRepository(\App\Entity\FieldOfStudy::class)->findAll()[0];
        $ap = $em->getRepository(\App\Entity\AdmissionPeriod::class)->findAll()[0];

        $payload = [
            'firstName' => 'NewApplicant',
            'lastName' => 'TestPerson',
            'email' => 'newapplicant-unique-'.uniqid().'@example.com',
            'phone' => '12345678',
            'fieldOfStudyId' => $fos->getId(),
            'admissionPeriodId' => $ap->getId(),
            'yearOfStudy' => '1. klasse',
        ];

        $client->request('POST', '/api/admin/applications', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertIsInt($data['id']);
    }

    public function testCreateApplicationValidationRejectsBlankFields(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        // Missing required fields
        $payload = [
            'firstName' => '',
            'lastName' => '',
            'email' => '',
        ];

        $client->request('POST', '/api/admin/applications', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateApplicationValidationRejectsInvalidEmail(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $fos = $em->getRepository(\App\Entity\FieldOfStudy::class)->findAll()[0];
        $ap = $em->getRepository(\App\Entity\AdmissionPeriod::class)->findAll()[0];

        $payload = [
            'firstName' => 'Test',
            'lastName' => 'User',
            'email' => 'not-an-email',
            'phone' => '12345678',
            'fieldOfStudyId' => $fos->getId(),
            'admissionPeriodId' => $ap->getId(),
        ];

        $client->request('POST', '/api/admin/applications', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateApplicationWithExistingUserEmail(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        // Use admin's email -- an existing user
        $container = static::getContainer();
        $em = $container->get(EntityManagerInterface::class);
        $existingUser = $em->getRepository(\App\Entity\User::class)->findOneBy(['user_name' => 'admin']);
        $existingEmail = $existingUser->getEmail();
        $fos = $em->getRepository(\App\Entity\FieldOfStudy::class)->findAll()[0];
        $ap = $em->getRepository(\App\Entity\AdmissionPeriod::class)->findAll()[0];

        $payload = [
            'firstName' => 'Admin',
            'lastName' => 'Existing',
            'email' => $existingEmail,
            'phone' => '99887766',
            'fieldOfStudyId' => $fos->getId(),
            'admissionPeriodId' => $ap->getId(),
            'yearOfStudy' => '1. klasse',
        ];

        $client->request('POST', '/api/admin/applications', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);

        // Verify the application is linked to the existing user
        $application = $em->getRepository(Application::class)->find($data['id']);
        $this->assertNotNull($application);
        $this->assertSame($existingUser->getId(), $application->getUser()->getId());
    }

    public function testCreateApplicationWithNonExistentAdmissionPeriodReturns422(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $fos = $em->getRepository(\App\Entity\FieldOfStudy::class)->findAll()[0];

        $payload = [
            'firstName' => 'Test',
            'lastName' => 'User',
            'email' => 'test-missing-ap@example.com',
            'phone' => '12345678',
            'fieldOfStudyId' => $fos->getId(),
            'admissionPeriodId' => 99999,
            'yearOfStudy' => '1. klasse',
        ];

        $client->request('POST', '/api/admin/applications', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateApplicationWithNonExistentFieldOfStudyReturns422(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $ap = $em->getRepository(\App\Entity\AdmissionPeriod::class)->findAll()[0];

        $payload = [
            'firstName' => 'Test',
            'lastName' => 'User',
            'email' => 'test-missing-fos@example.com',
            'phone' => '12345678',
            'fieldOfStudyId' => 99999,
            'admissionPeriodId' => $ap->getId(),
            'yearOfStudy' => '1. klasse',
        ];

        $client->request('POST', '/api/admin/applications', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    // --- Endpoint 9: DELETE /api/admin/applications/{id} ---

    public function testDeleteApplicationRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '/api/admin/applications/1', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testDeleteApplicationForbiddenForTeamMember(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();
        $client->request('DELETE', '/api/admin/applications/1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testDeleteApplicationSuccess(): void
    {
        $token = $this->getJwtToken('admin', '1234');
        $client = static::createClient();

        // First create an application to delete
        $container = static::getContainer();
        $em = $container->get(EntityManagerInterface::class);

        $admissionPeriod = $em->getRepository(\App\Entity\AdmissionPeriod::class)->findAll()[0];
        $application = new Application();
        $application->setAdmissionPeriod($admissionPeriod);
        $application->setYearOfStudy('1. klasse');
        $application->setHeardAboutFrom([]);
        $em->persist($application);
        $em->flush();
        $appId = $application->getId();

        $client->request('DELETE', '/api/admin/applications/'.$appId, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(204);

        // Verify it was actually deleted
        $em->clear();
        $deleted = $em->getRepository(Application::class)->find($appId);
        $this->assertNull($deleted);
    }

    public function testDeleteApplicationNotFoundReturns404(): void
    {
        $token = $this->getJwtToken('admin', '1234');
        $client = static::createClient();

        $client->request('DELETE', '/api/admin/applications/99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(404);
    }

    // --- Endpoint 10: POST /api/admin/applications/bulk-delete ---

    public function testBulkDeleteRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/admin/applications/bulk-delete', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['ids' => [1, 2]]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testBulkDeleteForbiddenForTeamMember(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();
        $client->request('POST', '/api/admin/applications/bulk-delete', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['ids' => [1, 2]]));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testBulkDeleteSuccess(): void
    {
        $token = $this->getJwtToken('admin', '1234');
        $client = static::createClient();

        // Create two applications to delete
        $container = static::getContainer();
        $em = $container->get(EntityManagerInterface::class);

        $admissionPeriod = $em->getRepository(\App\Entity\AdmissionPeriod::class)->findAll()[0];

        $app1 = new Application();
        $app1->setAdmissionPeriod($admissionPeriod);
        $app1->setYearOfStudy('1. klasse');
        $app1->setHeardAboutFrom([]);
        $em->persist($app1);

        $app2 = new Application();
        $app2->setAdmissionPeriod($admissionPeriod);
        $app2->setYearOfStudy('2. klasse');
        $app2->setHeardAboutFrom([]);
        $em->persist($app2);

        $em->flush();
        $id1 = $app1->getId();
        $id2 = $app2->getId();

        $client->request('POST', '/api/admin/applications/bulk-delete', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['ids' => [$id1, $id2]]));

        $this->assertResponseStatusCodeSame(204);

        // Verify both were deleted
        $em->clear();
        $this->assertNull($em->getRepository(Application::class)->find($id1));
        $this->assertNull($em->getRepository(Application::class)->find($id2));
    }

    public function testBulkDeleteSkipsNonExistentIds(): void
    {
        $token = $this->getJwtToken('admin', '1234');
        $client = static::createClient();

        // Mix of real and fake ids -- should still return 204
        $container = static::getContainer();
        $em = $container->get(EntityManagerInterface::class);

        $admissionPeriod = $em->getRepository(\App\Entity\AdmissionPeriod::class)->findAll()[0];

        $app = new Application();
        $app->setAdmissionPeriod($admissionPeriod);
        $app->setYearOfStudy('1. klasse');
        $app->setHeardAboutFrom([]);
        $em->persist($app);
        $em->flush();
        $appId = $app->getId();

        $client->request('POST', '/api/admin/applications/bulk-delete', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['ids' => [$appId, 99998, 99999]]));

        $this->assertResponseStatusCodeSame(204);

        // The real one should be deleted
        $em->clear();
        $this->assertNull($em->getRepository(Application::class)->find($appId));
    }

    public function testBulkDeleteWithEmptyIdsReturns204(): void
    {
        $token = $this->getJwtToken('admin', '1234');
        $client = static::createClient();

        $client->request('POST', '/api/admin/applications/bulk-delete', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['ids' => []]));

        $this->assertResponseStatusCodeSame(204);
    }

    public function testBulkDeleteRejectsNonIntegerIds(): void
    {
        $token = $this->getJwtToken('admin', '1234');
        $client = static::createClient();

        $client->request('POST', '/api/admin/applications/bulk-delete', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['ids' => ['abc', 'def']]));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateApplicationValidationRequiresFieldOfStudyAndAdmissionPeriod(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        // Valid name/email but missing fieldOfStudyId and admissionPeriodId
        $payload = [
            'firstName' => 'Test',
            'lastName' => 'User',
            'email' => 'test-missing-ids@example.com',
            'phone' => '12345678',
        ];

        $client->request('POST', '/api/admin/applications', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }
}
