<?php

namespace App\Tests\App\Scheduling\Infrastructure\Entity;

use App\Scheduling\Infrastructure\Entity\SchoolCapacity;
use App\Scheduling\Infrastructure\Entity\School;
use App\Shared\Entity\Semester;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SchoolCapacityUniqueConstraintTest extends KernelTestCase
{
    public function testDuplicateSchoolSemesterThrowsUniqueConstraintViolation(): void
    {
        self::bootKernel();
        $em = self::getContainer()->get('doctrine.orm.entity_manager');

        $school = $em->getRepository(School::class)->findOneBy([]);
        $semester = $em->getRepository(Semester::class)->findOneBy([]);

        if ($school === null || $semester === null) {
            $this->markTestSkipped('No school or semester fixture data found');
        }

        // Check if a capacity already exists for this school+semester
        $existing = $em->getRepository(SchoolCapacity::class)->findOneBy([
            'school' => $school,
            'semester' => $semester,
        ]);

        if ($existing === null) {
            // Create one first
            $cap = new SchoolCapacity();
            $cap->setSchool($school);
            $cap->setSemester($semester);
            $em->persist($cap);
            $em->flush();
        }

        // Now try to insert a duplicate
        $this->expectException(UniqueConstraintViolationException::class);

        $cap2 = new SchoolCapacity();
        $cap2->setSchool($school);
        $cap2->setSemester($semester);
        $em->persist($cap2);
        $em->flush();
    }
}
