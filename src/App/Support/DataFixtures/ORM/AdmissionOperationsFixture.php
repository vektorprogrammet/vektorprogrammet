<?php

declare(strict_types=1);

namespace App\Support\DataFixtures\ORM;

use App\Admission\Infrastructure\Entity\AdmissionPeriod;
use App\Organization\Infrastructure\Entity\Department;
use App\Organization\Infrastructure\Entity\FieldOfStudy;
use App\Shared\Entity\Semester;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class AdmissionOperationsFixture extends AbstractFixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['background-operations'];
    }

    public function load(ObjectManager $manager): void
    {
        $now = new \DateTime('now', new \DateTimeZone('Europe/Oslo'));
        $semester = $this->getCurrentSemester($manager, $now);

        $department = new Department();
        $department->setName('Background admission department');
        $department->setShortName('BG-ADM');
        $department->setEmail('background-admission@example.invalid');
        $department->setCity('BackgroundAdmissionCity');
        $department->setAddress('Disposable admission fixture');
        $department->setActive(true);
        $manager->persist($department);

        $fieldOfStudy = new FieldOfStudy();
        $fieldOfStudy->setName('Background admission studies');
        $fieldOfStudy->setShortName('BG-ADM-STUDY');
        $fieldOfStudy->setDepartment($department);
        $department->addFieldOfStudy($fieldOfStudy);
        $manager->persist($fieldOfStudy);

        $admissionPeriod = new AdmissionPeriod();
        $admissionPeriod->setDepartment($department);
        $admissionPeriod->setSemester($semester);
        $admissionPeriod->setStartDate((clone $now)->modify('-2 days'));
        $admissionPeriod->setEndDate((clone $now)->modify('+30 days'));
        $department->addAdmissionPeriod($admissionPeriod);
        $manager->persist($admissionPeriod);

        $manager->flush();
    }

    private function getCurrentSemester(ObjectManager $manager, \DateTime $now): Semester
    {
        $semester = $manager->getRepository(Semester::class)->findOneBy([
            'year' => $now->format('Y'),
            'semesterTime' => $now->format('m') <= 7 ? 'Vår' : 'Høst',
        ]);
        if ($semester instanceof Semester) {
            return $semester;
        }

        $semester = new Semester();
        $semester->setYear($now->format('Y'));
        $semester->setSemesterTime($now->format('m') <= 7 ? 'Vår' : 'Høst');
        $manager->persist($semester);

        return $semester;
    }
}
