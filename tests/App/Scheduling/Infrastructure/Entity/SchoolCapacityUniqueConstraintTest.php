<?php

namespace App\Tests\App\Scheduling\Infrastructure\Entity;

use App\Organization\Infrastructure\Entity\Department;
use App\Scheduling\Infrastructure\Entity\School;
use App\Scheduling\Infrastructure\Entity\SchoolCapacity;
use App\Shared\Entity\Semester;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * A school can only have one capacity row per semester PER DEPARTMENT.
 *
 * Department is part of the key on purpose: a production scan of
 * (school_id, semester_id) alone found 3 colliding groups, because one school
 * legitimately has capacity in one semester under two departments. Dropping
 * department from this key would make that valid data unrepresentable.
 */
class SchoolCapacityUniqueConstraintTest extends KernelTestCase
{
    public function testDuplicateSchoolSemesterDepartmentThrowsUniqueConstraintViolation(): void
    {
        self::bootKernel();
        $em = self::getContainer()->get('doctrine.orm.entity_manager');

        $school = $em->getRepository(School::class)->findOneBy([]);
        $semester = $em->getRepository(Semester::class)->findOneBy([]);
        $department = $em->getRepository(Department::class)->findOneBy([]);

        if (null === $school || null === $semester || null === $department) {
            $this->markTestSkipped('No school, semester or department fixture data found');
        }

        $existing = $em->getRepository(SchoolCapacity::class)->findOneBy([
            'school' => $school,
            'semester' => $semester,
            'department' => $department,
        ]);

        if (null === $existing) {
            $cap = new SchoolCapacity();
            $cap->setSchool($school);
            $cap->setSemester($semester);
            $cap->setDepartment($department);
            $em->persist($cap);
            $em->flush();
        }

        $this->expectException(UniqueConstraintViolationException::class);

        $duplicate = new SchoolCapacity();
        $duplicate->setSchool($school);
        $duplicate->setSemester($semester);
        $duplicate->setDepartment($department);
        $em->persist($duplicate);
        $em->flush();
    }

    public function testSameSchoolAndSemesterInAnotherDepartmentIsAllowed(): void
    {
        self::bootKernel();
        $em = self::getContainer()->get('doctrine.orm.entity_manager');

        $school = $em->getRepository(School::class)->findOneBy([]);
        $semester = $em->getRepository(Semester::class)->findOneBy([]);
        $departments = $em->getRepository(Department::class)->findBy([], ['id' => 'ASC'], 2);

        if (null === $school || null === $semester || 2 > \count($departments)) {
            $this->markTestSkipped('Needs a school, a semester and two departments in the fixtures');
        }

        foreach ($departments as $department) {
            $existing = $em->getRepository(SchoolCapacity::class)->findOneBy([
                'school' => $school,
                'semester' => $semester,
                'department' => $department,
            ]);

            if (null === $existing) {
                $capacity = new SchoolCapacity();
                $capacity->setSchool($school);
                $capacity->setSemester($semester);
                $capacity->setDepartment($department);
                $em->persist($capacity);
            }
        }

        $em->flush();

        $rows = $em->getRepository(SchoolCapacity::class)->findBy([
            'school' => $school,
            'semester' => $semester,
        ]);

        $this->assertGreaterThanOrEqual(
            2,
            \count($rows),
            'One school in one semester must be able to hold capacity under two departments'
        );
    }
}
