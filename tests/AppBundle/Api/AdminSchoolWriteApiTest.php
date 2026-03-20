<?php

namespace Tests\AppBundle\Api;

use App\Operations\Infrastructure\Entity\AssistantHistory;
use App\Entity\Department;
use App\Scheduling\Infrastructure\Entity\School;
use App\Shared\Entity\Semester;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Tests\BaseWebTestCase;

class AdminSchoolWriteApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    // --- POST /api/admin/schools (create school) ---

    public function testCreateSchoolRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/admin/schools', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => 'Test']));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testCreateSchoolForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');
        $client = static::createClient();
        $client->request('POST', '/api/admin/schools', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => 'Test']));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testCreateSchoolForbiddenForTeamMember(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();
        $client->request('POST', '/api/admin/schools', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => 'Test']));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testCreateSchoolSuccess(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $department = $em->getRepository(Department::class)->findAll()[0];

        $payload = [
            'name' => 'New Test School',
            'contactPerson' => 'Kari Nordmann',
            'email' => 'kari@school.no',
            'phone' => '12345678',
            'international' => false,
            'active' => true,
            'departmentId' => $department->getId(),
        ];

        $client->request('POST', '/api/admin/schools', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertIsInt($data['id']);
    }

    public function testCreateSchoolValidationRejectsBlankName(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $department = $em->getRepository(Department::class)->findAll()[0];

        $payload = [
            'name' => '',
            'contactPerson' => 'Kari',
            'email' => 'k@s.no',
            'phone' => '12345678',
            'departmentId' => $department->getId(),
        ];

        $client->request('POST', '/api/admin/schools', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateSchoolValidationRejectsInvalidEmail(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $department = $em->getRepository(Department::class)->findAll()[0];

        $payload = [
            'name' => 'Some School',
            'contactPerson' => 'Kari',
            'email' => 'not-an-email',
            'phone' => '12345678',
            'departmentId' => $department->getId(),
        ];

        $client->request('POST', '/api/admin/schools', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateSchoolValidationRejectsMissingDepartmentId(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        $payload = [
            'name' => 'School Without Dept',
            'contactPerson' => 'Kari',
            'email' => 'kari@school.no',
            'phone' => '12345678',
        ];

        $client->request('POST', '/api/admin/schools', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    // --- PUT /api/admin/schools/{id} (update school) ---

    public function testUpdateSchoolRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('PUT', '/api/admin/schools/1', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => 'Updated']));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testUpdateSchoolForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');
        $client = static::createClient();
        $client->request('PUT', '/api/admin/schools/1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => 'Updated']));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testUpdateSchoolForbiddenForTeamMember(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $school = $em->getRepository(School::class)->findAll()[0];

        $client->request('PUT', '/api/admin/schools/'.$school->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => 'Updated']));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testUpdateSchoolSuccess(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $school = $em->getRepository(School::class)->findAll()[0];

        $payload = [
            'name' => 'Updated School Name',
            'contactPerson' => 'Updated Person',
            'email' => 'updated@school.no',
            'phone' => '87654321',
            'international' => true,
            'active' => true,
        ];

        $client->request('PUT', '/api/admin/schools/'.$school->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(200);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('Updated School Name', $data['name']);
        $this->assertSame('Updated Person', $data['contactPerson']);
    }

    public function testUpdateSchoolNotFoundReturns404(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        $client->request('PUT', '/api/admin/schools/99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => 'Whatever']));

        $this->assertResponseStatusCodeSame(404);
    }

    public function testUpdateSchoolValidationRejectsBlankName(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $school = $em->getRepository(School::class)->findAll()[0];

        $client->request('PUT', '/api/admin/schools/'.$school->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => '']));

        $this->assertResponseStatusCodeSame(422);
    }

    // --- DELETE /api/admin/schools/{id} ---

    public function testDeleteSchoolRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '/api/admin/schools/1', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testDeleteSchoolForbiddenForTeamMember(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();
        $client->request('DELETE', '/api/admin/schools/1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testDeleteSchoolForbiddenForTeamLeader(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();
        $client->request('DELETE', '/api/admin/schools/1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testDeleteSchoolSuccess(): void
    {
        $token = $this->getJwtToken('admin', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);

        // Create a fresh school with no FK dependencies
        $school = new School();
        $school->setName('School to Delete');
        $school->setContactPerson('Nobody');
        $school->setEmail('delete@school.no');
        $school->setPhone('00000000');
        $em->persist($school);
        $em->flush();
        $schoolId = $school->getId();

        $client->request('DELETE', '/api/admin/schools/'.$schoolId, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(204);

        $em->clear();
        $deleted = $em->getRepository(School::class)->find($schoolId);
        $this->assertNull($deleted);
    }

    public function testDeleteSchoolNotFoundReturns404(): void
    {
        $token = $this->getJwtToken('admin', '1234');
        $client = static::createClient();

        $client->request('DELETE', '/api/admin/schools/99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(404);
    }

    // --- POST /api/admin/schools/{id}/assistants (delegate user to school) ---

    public function testDelegateAssistantRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/admin/schools/1/assistants', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['userId' => 1]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testDelegateAssistantForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');
        $client = static::createClient();

        // Provider runs before security -- need a real school for the provider to succeed
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $school = $em->getRepository(School::class)->findAll()[0];

        $client->request('POST', '/api/admin/schools/'.$school->getId().'/assistants', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['userId' => 1]));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testDelegateAssistantForbiddenForTeamMember(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $school = $em->getRepository(School::class)->findAll()[0];

        $client->request('POST', '/api/admin/schools/'.$school->getId().'/assistants', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['userId' => 1]));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testDelegateAssistantSuccess(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $school = $em->getRepository(School::class)->findAll()[0];
        $user = $em->getRepository(User::class)->findOneBy(['user_name' => 'teammember']);
        $semester = $em->getRepository(Semester::class)->findAll()[0];

        $payload = [
            'userId' => $user->getId(),
            'semesterId' => $semester->getId(),
            'workdays' => '4',
            'bolk' => 'Bolk 1',
            'day' => 'Mandag',
        ];

        $client->request('POST', '/api/admin/schools/'.$school->getId().'/assistants', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
    }

    public function testDelegateAssistantSchoolNotFoundReturns404(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        $payload = [
            'userId' => 1,
            'semesterId' => 1,
            'workdays' => '4',
            'bolk' => 'Bolk 1',
            'day' => 'Mandag',
        ];

        $client->request('POST', '/api/admin/schools/99999/assistants', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(404);
    }

    public function testDelegateAssistantValidationRejectsBlankWorkdays(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $school = $em->getRepository(School::class)->findAll()[0];
        $user = $em->getRepository(User::class)->findOneBy(['user_name' => 'teammember']);
        $semester = $em->getRepository(Semester::class)->findAll()[0];

        $payload = [
            'userId' => $user->getId(),
            'semesterId' => $semester->getId(),
            'workdays' => '',
            'bolk' => 'Bolk 1',
            'day' => 'Mandag',
        ];

        $client->request('POST', '/api/admin/schools/'.$school->getId().'/assistants', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testDelegateAssistantInvalidUserIdReturns422(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $school = $em->getRepository(School::class)->findAll()[0];
        $semester = $em->getRepository(Semester::class)->findAll()[0];

        $payload = [
            'userId' => 99999,
            'semesterId' => $semester->getId(),
            'workdays' => '4',
            'bolk' => 'Bolk 1',
            'day' => 'Mandag',
        ];

        $client->request('POST', '/api/admin/schools/'.$school->getId().'/assistants', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testDelegateAssistantInvalidSemesterIdReturns422(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $school = $em->getRepository(School::class)->findAll()[0];
        $user = $em->getRepository(User::class)->findOneBy(['user_name' => 'teammember']);

        $payload = [
            'userId' => $user->getId(),
            'semesterId' => 99999,
            'workdays' => '4',
            'bolk' => 'Bolk 1',
            'day' => 'Mandag',
        ];

        $client->request('POST', '/api/admin/schools/'.$school->getId().'/assistants', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    // --- DELETE /api/admin/assistant-histories/{id} ---

    public function testDeleteAssistantHistoryRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '/api/admin/assistant-histories/1', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testDeleteAssistantHistoryForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');
        $client = static::createClient();

        // Provider runs before security -- need a real entity for the provider to succeed
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $ah = $em->getRepository(AssistantHistory::class)->findAll()[0];
        $this->assertNotNull($ah);

        $client->request('DELETE', '/api/admin/assistant-histories/'.$ah->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testDeleteAssistantHistoryForbiddenForTeamMember(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $ah = $em->getRepository(AssistantHistory::class)->findAll()[0];
        $this->assertNotNull($ah);

        $client->request('DELETE', '/api/admin/assistant-histories/'.$ah->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testDeleteAssistantHistorySuccess(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);

        // Create a fresh assistant history to delete
        $user = $em->getRepository(User::class)->findOneBy(['user_name' => 'teammember']);
        $school = $em->getRepository(School::class)->findAll()[0];
        $semester = $em->getRepository(Semester::class)->findAll()[0];
        $department = $em->getRepository(Department::class)->findAll()[0];

        $ah = new AssistantHistory();
        $ah->setUser($user);
        $ah->setSchool($school);
        $ah->setSemester($semester);
        $ah->setDepartment($department);
        $ah->setWorkdays('4');
        $ah->setBolk('Bolk 1');
        $ah->setDay('Tirsdag');
        $em->persist($ah);
        $em->flush();
        $ahId = $ah->getId();

        $client->request('DELETE', '/api/admin/assistant-histories/'.$ahId, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(204);

        $em->clear();
        $deleted = $em->getRepository(AssistantHistory::class)->find($ahId);
        $this->assertNull($deleted);
    }

    public function testDeleteAssistantHistoryNotFoundReturns404(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        $client->request('DELETE', '/api/admin/assistant-histories/99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(404);
    }
}
