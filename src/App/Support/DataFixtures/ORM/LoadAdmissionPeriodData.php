<?php

/**
 * Created by IntelliJ IDEA.
 * User: sigtot
 * Date: 02.10.18
 * Time: 19:12.
 */

namespace App\Support\DataFixtures\ORM;

use App\Entity\AdmissionPeriod;
use App\Entity\Department;
use App\Shared\Entity\Semester;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LoadAdmissionPeriodData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $now = new \DateTime();
        $jan = 1;
        $jul = 7;
        $aug = 8;
        $isSpring = $now->format('n') <= $jul;

        $ap = new AdmissionPeriod();
        $ap->setSemester($this->getReference('semester-current', Semester::class));
        $ap->setDepartment($this->getReference('dep-1', Department::class));
        $ap->setStartDate(new \DateTime());
        $ap->setEndDate(new \DateTime());
        $ap->getStartDate()->modify('-1day');
        $ap->getEndDate()->modify('+1day');
        $manager->persist($ap);
        $this->addReference('admission-period-current', $ap);

        $ap = new AdmissionPeriod();
        $ap->setSemester($this->getReference('semester-current', Semester::class));
        $ap->setDepartment($this->getReference('dep-4', Department::class));
        $ap->setStartDate(new \DateTime());
        $ap->setEndDate(new \DateTime());
        $ap->getStartDate()->modify('-1day');
        $ap->getEndDate()->modify('+1day');
        $manager->persist($ap);
        $this->addReference('uio-admission-period-current', $ap);

        $ap = new AdmissionPeriod();
        $ap->setSemester($this->getReference('semester-previous', Semester::class));
        $ap->setDepartment($this->getReference('dep-1', Department::class));
        $ap->setStartDate(new \DateTime());
        $ap->setEndDate(new \DateTime());
        $ap->getStartDate()->setDate($ap->getSemester()->getYear(), $isSpring ? $aug : $jan, 1);
        $ap->getEndDate()->setDate($ap->getSemester()->getYear(), $isSpring ? $aug : $jan, 5);
        $manager->persist($ap);
        $this->addReference('admission-period-previous', $ap);

        $ap = new AdmissionPeriod();
        $ap->setSemester($this->getReference('semester-1', Semester::class));
        $ap->setDepartment($this->getReference('dep-1', Department::class));
        $ap->setStartDate(new \DateTime('2013-01-01'));
        $ap->setEndDate(new \DateTime('2013-01-05'));
        $manager->persist($ap);
        $this->addReference('admission-period-1', $ap);

        $ap = new AdmissionPeriod();
        $ap->setSemester($this->getReference('semester-2', Semester::class));
        $ap->setDepartment($this->getReference('dep-2', Department::class));
        $ap->setStartDate(new \DateTime('2015-01-01'));
        $ap->setEndDate(new \DateTime('2015-05-30'));
        $manager->persist($ap);
        $this->addReference('admission-period-2', $ap);

        $ap = new AdmissionPeriod();
        $ap->setSemester($this->getReference('semester-3', Semester::class));
        $ap->setDepartment($this->getReference('dep-3', Department::class));
        $ap->setStartDate(new \DateTime('2015-01-01'));
        $ap->setEndDate(new \DateTime('2015-05-30'));
        $manager->persist($ap);
        $this->addReference('admission-period-3', $ap);

        $ap = new AdmissionPeriod();
        $ap->setSemester($this->getReference('semester-3', Semester::class));
        $ap->setDepartment($this->getReference('dep-4', Department::class));
        $ap->setStartDate(new \DateTime('2015-01-01'));
        $ap->setEndDate(new \DateTime('2015-02-01'));
        $ap->getEndDate()->modify('+1day');
        $manager->persist($ap);
        $this->addReference('admission-period-4', $ap);

        $ap = new AdmissionPeriod();
        $ap->setSemester($this->getReference('semester-3', Semester::class));
        $ap->setDepartment($this->getReference('dep-1', Department::class));
        $ap->setStartDate(new \DateTime('2014-08-01'));
        $ap->setEndDate(new \DateTime('2014-12-30'));
        $manager->persist($ap);
        $this->addReference('admission-period-5', $ap);

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 4;
    }
}
