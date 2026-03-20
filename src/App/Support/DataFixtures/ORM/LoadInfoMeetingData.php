<?php

declare(strict_types=1);

namespace App\Support\DataFixtures\ORM;

use App\Admission\Infrastructure\Entity\AdmissionPeriod;
use App\Admission\Infrastructure\Entity\InfoMeeting;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadInfoMeetingData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    public function setContainer(?ContainerInterface $container = null): void
    {
        // container not used; method required by ContainerAwareInterface
    }

    public function load(ObjectManager $manager): void
    {
        $infoMeetingUiO = new InfoMeeting();
        $date = new \DateTime('now');
        $date->modify('+1day');
        $infoMeetingUiO->setShowOnPage(true);
        $infoMeetingUiO->setDate($date);
        $infoMeetingUiO->setRoom('Parken');
        $infoMeetingUiO->setDescription('Det blir underholdning!');

        $semester = $this->getReference('uio-admission-period-current', AdmissionPeriod::class);
        $semester->setInfoMeeting($infoMeetingUiO);

        $manager->persist($infoMeetingUiO);
        $manager->persist($semester);

        $manager->flush();
    }

    /**
     * Get the order of this fixture.
     */
    public function getOrder(): int
    {
        return 28;
    }
}
