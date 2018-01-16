<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\InfoMeeting;
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
        $infoMeetingNMBU = new InfoMeeting();
        $infoMeetingNMBU->setDate("03/02");
        $infoMeetingNMBU->setTime("15:30");
        $infoMeetingNMBU->setRoom("Parken");
        $infoMeetingNMBU->setExtra("Det blir underholdning!");
        $infoMeetingNMBU->setDepartment($this->getReference('dep-3'));

        $department = $this->getReference('dep-3');
        $department->setInfoMeeting($infoMeetingNMBU);

        $manager->persist($infoMeetingNMBU);
        $manager->persist($department);

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
