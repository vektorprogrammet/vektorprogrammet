<?php

namespace App\Tests\App\Operations\Infrastructure\Entity;

use App\Identity\Infrastructure\Entity\User;
use App\Operations\Infrastructure\Entity\AssistantHistory;
use App\Organization\Infrastructure\Entity\Department;
use App\Scheduling\Infrastructure\Entity\School;
use App\Shared\Entity\Semester;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AssistantHistoryUniqueConstraintTest extends KernelTestCase
{
    public function testDuplicateUserSchoolSemesterThrowsUniqueConstraintViolation(): void
    {
        self::bootKernel();
        $em = self::getContainer()->get('doctrine.orm.entity_manager');

        $user = $em->getRepository(User::class)->findOneBy([]);
        $school = $em->getRepository(School::class)->findOneBy([]);
        $semester = $em->getRepository(Semester::class)->findOneBy([]);
        $department = $em->getRepository(Department::class)->findOneBy([]);

        if ($user === null || $school === null || $semester === null || $department === null) {
            $this->markTestSkipped('Missing fixture data');
        }

        // Remove any existing history for this user/school/semester combination
        $existingHistories = $em->getRepository(AssistantHistory::class)->findBy([
            'user' => $user,
            'school' => $school,
            'semester' => $semester,
        ]);
        foreach ($existingHistories as $existing) {
            $em->remove($existing);
        }
        $em->flush();

        // Insert first
        $ah1 = new AssistantHistory();
        $ah1->setUser($user);
        $ah1->setSchool($school);
        $ah1->setSemester($semester);
        $ah1->setDepartment($department);
        $ah1->setWorkdays('4');
        $ah1->setBolk('Bolk 1');
        $ah1->setDay('Mandag');
        $em->persist($ah1);
        $em->flush();

        // Try to insert duplicate
        $this->expectException(UniqueConstraintViolationException::class);

        $ah2 = new AssistantHistory();
        $ah2->setUser($user);
        $ah2->setSchool($school);
        $ah2->setSemester($semester);
        $ah2->setDepartment($department);
        $ah2->setWorkdays('2');
        $ah2->setBolk('Bolk 2');
        $ah2->setDay('Tirsdag');
        $em->persist($ah2);
        $em->flush();
    }
}
