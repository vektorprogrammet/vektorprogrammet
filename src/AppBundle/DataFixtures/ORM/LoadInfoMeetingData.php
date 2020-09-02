<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\InfoMeeting;
use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadInfoMeetingData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $infoMeetingUiO = new InfoMeeting();
        $date = new DateTime('now');
        $date->modify('+1day');
        $infoMeetingUiO->setShowOnPage(true);
        $infoMeetingUiO->setDate($date);
        $infoMeetingUiO->setRoom("Parken");
        $infoMeetingUiO->setDescription("Det blir underholdning!");

        $semester = $this->getReference('uio-admission-period-current');
        $semester->setInfoMeeting($infoMeetingUiO);

        $manager->persist($infoMeetingUiO);
        $manager->persist($semester);

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 28;
    }
}
