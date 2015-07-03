<?php


namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Forum;


class LoadForumData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $forum1 = new Forum();
        $forum1->setName('Skoler');
        $forum1->setDescription('Diskusjon for de ulike ungdommskolene');
        $forum1->setType("school");

        $manager->persist($forum1);

        $manager->flush();

        $this->addReference('forum-1', $forum1);
    }

    public function getOrder()
    {
        return 1;
    }
}