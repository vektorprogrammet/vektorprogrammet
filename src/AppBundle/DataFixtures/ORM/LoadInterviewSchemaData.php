<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\InterviewSchema;

class LoadInterviewSchemaData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $schema1 = new InterviewSchema();
        $schema1->setName("Intervjuskjema HiST, 2015");
        $schema1->addInterviewQuestion($this->getReference('iq-1'));
        $schema1->addInterviewQuestion($this->getReference('iq-2'));
        $manager->persist($schema1);

        $schema2 = new InterviewSchema();
        $schema2->setName("Intervjuskjema NTNU, 2015");
        $schema2->addInterviewQuestion($this->getReference('iq-1'));
        $schema2->addInterviewQuestion($this->getReference('iq-2'));
        $schema2->addInterviewQuestion($this->getReference('iq-3'));
        $schema2->addInterviewQuestion($this->getReference('iq-4'));
        $schema2->addInterviewQuestion($this->getReference('iq-5'));
        $schema2->addInterviewQuestion($this->getReference('iq-6'));
        $schema2->addInterviewQuestion($this->getReference('iq-7'));
        $schema2->addInterviewQuestion($this->getReference('iq-8'));
        $manager->persist($schema2);

        $manager->flush();

        $this->setReference('ischema-1', $schema1);
        $this->setReference('ischema-2', $schema2);
    }

    public function getOrder()
    {
        return 3;
    }
}