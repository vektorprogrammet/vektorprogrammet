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
        $infoMeetingUiO = new InfoMeeting();
        $infoMeetingUiO->setDate(new \DateTime('now'));
        $infoMeetingUiO->setRoom("Parken");
        $infoMeetingUiO->setExtra("Det blir underholdning!");
        $infoMeetingUiO->setDepartment($this->getReference('dep-4'));

        $department = $this->getReference('dep-4');
        $department->setInfoMeeting($infoMeetingUiO);

        $manager->persist($infoMeetingUiO);
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
