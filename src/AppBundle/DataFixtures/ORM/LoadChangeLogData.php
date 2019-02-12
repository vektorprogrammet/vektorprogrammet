<?php

namespace AppBundle\DataFixtures\ORM;



use AppBundle\Entity\ChangeLogItem;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadChangeLogData extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 1;
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        $changeLogItem = new ChangeLogItem();
        $changeLogItem->setTitle('Nye teammedlemmer blir automatisk invitert til Slack');
        $changeLogItem->setDescription('Når et nytt medlem blir lagt til i team får han/hun tilgang til sin @vektorprogrammet.no-epost ved å logge inn på gmail.com med sin bruker på vektorprogrammet.no. Når det nye medlemmet logger inn på Gmail for første gang vil det ligge en invitasjon til Slack der.');
        $changeLogItem->setGithubLink('https://github.com');
        $changeLogItem->setDate(new \DateTime());

        $manager->persist($changeLogItem);
        $manager->flush();

    }
}