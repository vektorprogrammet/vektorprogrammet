<?php

namespace App\Tests\App\Operations\Infrastructure\Entity;

use App\Identity\Infrastructure\Entity\User;
use App\Operations\Infrastructure\Entity\AssistantHistory;
use App\Organization\Infrastructure\Entity\Department;
use App\Scheduling\Infrastructure\Entity\School;
use App\Shared\Entity\Semester;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * An assistant is placed at a school once PER BOLK.
 *
 * A semester has two teaching blocks (bolk 1 and bolk 2) and an assistant is sent out once
 * per assigned bolk, possibly on a different weekday each time. So one person doing both
 * bolks at one school is two legitimate placements, not a duplicate. The key includes bolk
 * for exactly that reason; unique(user, school, semester) would make valid data
 * unrepresentable.
 */
class AssistantHistoryUniqueConstraintTest extends KernelTestCase
{
    public function testSameBolkTwiceThrowsUniqueConstraintViolation(): void
    {
        $em = $this->bootAndClearHistory($user, $school, $semester, $department);

        $em->persist($this->placement($user, $school, $semester, $department, 'Bolk 1', 'Mandag', '4'));
        $em->flush();

        $this->expectException(UniqueConstraintViolationException::class);

        $em->persist($this->placement($user, $school, $semester, $department, 'Bolk 1', 'Tirsdag', '2'));
        $em->flush();
    }

    public function testBothBolksAtTheSameSchoolAreTwoValidPlacements(): void
    {
        $em = $this->bootAndClearHistory($user, $school, $semester, $department);

        $em->persist($this->placement($user, $school, $semester, $department, 'Bolk 1', 'Mandag', '4'));
        $em->persist($this->placement($user, $school, $semester, $department, 'Bolk 2', 'Torsdag', '2'));
        $em->flush();

        $placements = $em->getRepository(AssistantHistory::class)->findBy([
            'user' => $user,
            'school' => $school,
            'semester' => $semester,
        ]);

        $this->assertCount(
            2,
            $placements,
            'One assistant must be able to hold both bolks at one school in one semester'
        );
    }

    private function bootAndClearHistory(?User &$user, ?School &$school, ?Semester &$semester, ?Department &$department): \Doctrine\ORM\EntityManagerInterface
    {
        self::bootKernel();
        $em = self::getContainer()->get('doctrine.orm.entity_manager');

        $user = $em->getRepository(User::class)->findOneBy([]);
        $school = $em->getRepository(School::class)->findOneBy([]);
        $semester = $em->getRepository(Semester::class)->findOneBy([]);
        $department = $em->getRepository(Department::class)->findOneBy([]);

        if (null === $user || null === $school || null === $semester || null === $department) {
            $this->markTestSkipped('Missing fixture data');
        }

        foreach ($em->getRepository(AssistantHistory::class)->findBy(['user' => $user, 'school' => $school, 'semester' => $semester]) as $existing) {
            $em->remove($existing);
        }
        $em->flush();

        return $em;
    }

    private function placement(User $user, School $school, Semester $semester, Department $department, string $bolk, string $day, string $workdays): AssistantHistory
    {
        $placement = new AssistantHistory();
        $placement->setUser($user);
        $placement->setSchool($school);
        $placement->setSemester($semester);
        $placement->setDepartment($department);
        $placement->setWorkdays($workdays);
        $placement->setBolk($bolk);
        $placement->setDay($day);

        return $placement;
    }
}
