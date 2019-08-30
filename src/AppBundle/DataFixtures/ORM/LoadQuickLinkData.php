<?php


namespace AppBundle\DataFixtures\ORM;


use AppBundle\Entity\QuickLink;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadQuickLinkData extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $quickLink = new QuickLink();
        $quickLink
            ->setOrderNum(1)
            ->setTitle("Link til drive")
            ->setUrl("https://drive.google.com")
            ->setVisible(true)
            ->setIconUrl('images/drive.png' )
            ->setOwner($this->getReference('user-admin'));
        $manager->persist($quickLink);

        $quickLink = new QuickLink();
        $quickLink
            ->setOrderNum(3)
            ->setTitle("Link til gmail")
            ->setUrl("https://mail.google.com")
            ->setVisible(true)
            ->setIconUrl('images/gmail.png')
            ->setOwner($this->getReference('user-admin'));
        $manager->persist($quickLink);


        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 5;
    }
}
