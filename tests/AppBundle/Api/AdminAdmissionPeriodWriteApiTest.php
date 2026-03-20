<?php

namespace Tests\AppBundle\Api;

use App\Admission\Infrastructure\Entity\AdmissionPeriod;
use App\Entity\Department;
use App\Admission\Infrastructure\Entity\InfoMeeting;
use App\Shared\Entity\Semester;
use Doctrine\ORM\EntityManagerInterface;
use Tests\BaseWebTestCase;

class AdminAdmissionPeriodWriteApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    // --- POST /api/admin/admission-periods ---

    public function testCreateAdmissionPeriodRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/admin/admission-periods', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['departmentId' => 1, 'semesterId' => 1, 'startDate' => '2099-01-01', 'endDate' => '2099-06-01']));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testCreateAdmissionPeriodForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');
        $client = static::createClient();
        $client->request('POST', '/api/admin/admission-periods', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['departmentId' => 1, 'semesterId' => 1, 'startDate' => '2099-01-01', 'endDate' => '2099-06-01']));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testCreateAdmissionPeriodSuccess(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);

        // Use dep-2 (UiB) + semester 'Vår 2013' which has no admission period for UiB
        $dept = $em->getRepository(Department::class)->findOneBy(['shortName' => 'UiB']);
        $semester = $em->getRepository(Semester::class)->findOneBy(['semesterTime' => 'Vår', 'year' => '2013']);

        $payload = [
            'departmentId' => $dept->getId(),
            'semesterId' => $semester->getId(),
            'startDate' => '2013-01-15',
            'endDate' => '2013-05-15',
        ];

        $client->request('POST', '/api/admin/admission-periods', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertIsInt($data['id']);
    }

    public function testCreateAdmissionPeriodValidationRejectsBlankDates(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $payload = [
            'departmentId' => 1,
            'semesterId' => 1,
            'startDate' => '',
            'endDate' => '',
        ];

        $client->request('POST', '/api/admin/admission-periods', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateAdmissionPeriodDuplicateReturns409(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);

        // dep-1 (NTNU) + semester-1 ('Vår 2013') already has an admission period in fixtures
        $dept = $em->getRepository(Department::class)->findOneBy(['shortName' => 'NTNU']);
        $semester = $em->getRepository(Semester::class)->findOneBy(['semesterTime' => 'Vår', 'year' => '2013']);

        $payload = [
            'departmentId' => $dept->getId(),
            'semesterId' => $semester->getId(),
            'startDate' => '2013-02-01',
            'endDate' => '2013-04-01',
        ];

        $client->request('POST', '/api/admin/admission-periods', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(409);
    }

    public function testCreateAdmissionPeriodWithNonExistentDepartmentReturns422(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $semester = $em->getRepository(Semester::class)->findOneBy(['semesterTime' => 'Vår', 'year' => '2013']);

        $payload = [
            'departmentId' => 99999,
            'semesterId' => $semester->getId(),
            'startDate' => '2013-01-15',
            'endDate' => '2013-05-15',
        ];

        $client->request('POST', '/api/admin/admission-periods', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateAdmissionPeriodWithNonExistentSemesterReturns422(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $dept = $em->getRepository(Department::class)->findOneBy(['shortName' => 'UiB']);

        $payload = [
            'departmentId' => $dept->getId(),
            'semesterId' => 99999,
            'startDate' => '2013-01-15',
            'endDate' => '2013-05-15',
        ];

        $client->request('POST', '/api/admin/admission-periods', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    // --- PUT /api/admin/admission-periods/{id} ---

    public function testUpdateAdmissionPeriodRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('PUT', '/api/admin/admission-periods/1', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['startDate' => '2025-01-01', 'endDate' => '2025-06-01']));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testUpdateAdmissionPeriodForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');
        $client = static::createClient();
        $client->request('PUT', '/api/admin/admission-periods/1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['startDate' => '2025-01-01', 'endDate' => '2025-06-01']));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testUpdateAdmissionPeriodSuccess(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $ap = $em->getRepository(AdmissionPeriod::class)->findAll()[0];

        $payload = [
            'startDate' => '2025-02-01',
            'endDate' => '2025-07-01',
        ];

        $client->request('PUT', '/api/admin/admission-periods/'.$ap->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(200);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
    }

    public function testUpdateAdmissionPeriodNotFoundReturns404(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $client->request('PUT', '/api/admin/admission-periods/99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['startDate' => '2025-01-01', 'endDate' => '2025-06-01']));

        $this->assertResponseStatusCodeSame(404);
    }

    public function testUpdateAdmissionPeriodValidationRejectsBlankDates(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $ap = $em->getRepository(AdmissionPeriod::class)->findAll()[0];

        $payload = [
            'startDate' => '',
            'endDate' => '',
        ];

        $client->request('PUT', '/api/admin/admission-periods/'.$ap->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    // --- DELETE /api/admin/admission-periods/{id} ---

    public function testDeleteAdmissionPeriodRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '/api/admin/admission-periods/1', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testDeleteAdmissionPeriodForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');
        $client = static::createClient();
        $client->request('DELETE', '/api/admin/admission-periods/1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testDeleteAdmissionPeriodSuccess(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        // Create a fresh admission period to delete
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $dept = $em->getRepository(Department::class)->findOneBy(['shortName' => 'UiO']);
        $semester = $em->getRepository(Semester::class)->findOneBy(['semesterTime' => 'Vår', 'year' => '2013']);

        $ap = new AdmissionPeriod();
        $ap->setDepartment($dept);
        $ap->setSemester($semester);
        $ap->setStartDate(new \DateTime('2013-02-01'));
        $ap->setEndDate(new \DateTime('2013-05-01'));
        $em->persist($ap);
        $em->flush();
        $apId = $ap->getId();

        $client->request('DELETE', '/api/admin/admission-periods/'.$apId, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(204);

        $em->clear();
        $deleted = $em->getRepository(AdmissionPeriod::class)->find($apId);
        $this->assertNull($deleted);
    }

    public function testDeleteAdmissionPeriodWithInfoMeetingCascades(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        // Create admission period with an info meeting
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $dept = $em->getRepository(Department::class)->findOneBy(['shortName' => 'UiO']);
        $semester = $em->getRepository(Semester::class)->findOneBy(['semesterTime' => 'Vår', 'year' => '2015']);

        $infoMeeting = new InfoMeeting();
        $infoMeeting->setShowOnPage(true);
        $infoMeeting->setDate(new \DateTime('+7 days'));
        $infoMeeting->setRoom('Test Room');
        $em->persist($infoMeeting);

        $ap = new AdmissionPeriod();
        $ap->setDepartment($dept);
        $ap->setSemester($semester);
        $ap->setStartDate(new \DateTime('2015-02-01'));
        $ap->setEndDate(new \DateTime('2015-05-01'));
        $ap->setInfoMeeting($infoMeeting);
        $em->persist($ap);
        $em->flush();

        $apId = $ap->getId();
        $infoMeetingId = $infoMeeting->getId();

        $client->request('DELETE', '/api/admin/admission-periods/'.$apId, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(204);

        $em->clear();
        $this->assertNull($em->getRepository(AdmissionPeriod::class)->find($apId));
        $this->assertNull($em->getRepository(InfoMeeting::class)->find($infoMeetingId));
    }

    public function testDeleteAdmissionPeriodNotFoundReturns404(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $client->request('DELETE', '/api/admin/admission-periods/99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(404);
    }
}
