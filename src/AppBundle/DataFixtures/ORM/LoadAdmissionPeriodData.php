<?php
/**
 * Created by IntelliJ IDEA.
 * User: sigtot
 * Date: 02.10.18
 * Time: 19:12
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\AdmissionPeriod;
use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadAdmissionPeriodData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $now = new DateTime();
        $jan = 1;
        $jul = 7;
        $aug = 8;
        $isSpring = $now->format('n') <= $jul;

        $ap = new AdmissionPeriod();
        $ap->setSemester($this->getReference('semester-current'));
        $ap->setDepartment($this->getReference('dep-1'));
        $ap->setStartDate(new DateTime());
        $ap->setEndDate(new DateTime());
        $ap->getStartDate()->modify('-1day');
        $ap->getEndDate()->modify('+1day');
        $manager->persist($ap);
        $this->addReference('admission-period-current', $ap);

        $ap = new AdmissionPeriod();
        $ap->setSemester($this->getReference('semester-current'));
        $ap->setDepartment($this->getReference('dep-4'));
        $ap->setStartDate(new DateTime());
        $ap->setEndDate(new DateTime());
        $ap->getStartDate()->modify('-1day');
        $ap->getEndDate()->modify('+1day');
        $manager->persist($ap);
        $this->addReference('uio-admission-period-current', $ap);

        $ap = new AdmissionPeriod();
        $ap->setSemester($this->getReference('semester-previous'));
        $ap->setDepartment($this->getReference('dep-1'));
        $ap->setStartDate(new DateTime());
        $ap->setEndDate(new DateTime());
        $ap->getStartDate()->setDate($ap->getSemester()->getYear(), $isSpring ? $aug : $jan, 1);
        $ap->getEndDate()->setDate($ap->getSemester()->getYear(), $isSpring ? $aug : $jan, 5);
        $manager->persist($ap);
        $this->addReference('admission-period-previous', $ap);

        $ap = new AdmissionPeriod();
        $ap->setSemester($this->getReference('semester-1'));
        $ap->setDepartment($this->getReference('dep-1'));
        $ap->setStartDate(new DateTime('2013-01-01'));
        $ap->setEndDate(new DateTime('2013-01-05'));
        $manager->persist($ap);
        $this->addReference('admission-period-1', $ap);

        $ap = new AdmissionPeriod();
        $ap->setSemester($this->getReference('semester-2'));
        $ap->setDepartment($this->getReference('dep-2'));
        $ap->setStartDate(new DateTime('2015-01-01'));
        $ap->setEndDate(new DateTime('2015-05-30'));
        $manager->persist($ap);
        $this->addReference('admission-period-2', $ap);

        $ap = new AdmissionPeriod();
        $ap->setSemester($this->getReference('semester-3'));
        $ap->setDepartment($this->getReference('dep-3'));
        $ap->setStartDate(new DateTime('2015-01-01'));
        $ap->setEndDate(new DateTime('2015-05-30'));
        $manager->persist($ap);
        $this->addReference('admission-period-3', $ap);

        $ap = new AdmissionPeriod();
        $ap->setSemester($this->getReference('semester-3'));
        $ap->setDepartment($this->getReference('dep-4'));
        $ap->setStartDate(new DateTime('2015-01-01'));
        $ap->setEndDate(new DateTime('2015-02-01'));
        $ap->getEndDate()->modify('+1day');
        $manager->persist($ap);
        $this->addReference('admission-period-4', $ap);

        $ap = new AdmissionPeriod();
        $ap->setSemester($this->getReference('semester-3'));
        $ap->setDepartment($this->getReference('dep-1'));
        $ap->setStartDate(new DateTime('2014-08-01'));
        $ap->setEndDate(new DateTime('2014-12-30'));
        $manager->persist($ap);
        $this->addReference('admission-period-5', $ap);

        $manager->flush();
    }

    public function getOrder()
    {
        return 4;
    }
}
