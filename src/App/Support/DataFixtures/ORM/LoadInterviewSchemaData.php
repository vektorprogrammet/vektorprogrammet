<?php

namespace App\Support\DataFixtures\ORM;

use App\Interview\Infrastructure\Entity\InterviewQuestion;
use App\Interview\Infrastructure\Entity\InterviewSchema;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LoadInterviewSchemaData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $schema1 = new InterviewSchema();
        $schema1->setName('Intervjuskjema HiST, 2015');
        $schema1->addInterviewQuestion($this->getReference('iq-1', InterviewQuestion::class));
        $schema1->addInterviewQuestion($this->getReference('iq-2', InterviewQuestion::class));
        $manager->persist($schema1);

        $schema2 = new InterviewSchema();
        $schema2->setName('Intervjuskjema NTNU, 2015');
        $schema2->addInterviewQuestion($this->getReference('iq-1', InterviewQuestion::class));
        $schema2->addInterviewQuestion($this->getReference('iq-2', InterviewQuestion::class));
        $schema2->addInterviewQuestion($this->getReference('iq-3', InterviewQuestion::class));
        $schema2->addInterviewQuestion($this->getReference('iq-4', InterviewQuestion::class));
        $schema2->addInterviewQuestion($this->getReference('iq-5', InterviewQuestion::class));
        $schema2->addInterviewQuestion($this->getReference('iq-6', InterviewQuestion::class));
        $schema2->addInterviewQuestion($this->getReference('iq-7', InterviewQuestion::class));
        $schema2->addInterviewQuestion($this->getReference('iq-8', InterviewQuestion::class));
        $manager->persist($schema2);

        $manager->flush();

        $this->setReference('ischema-1', $schema1);
        $this->setReference('ischema-2', $schema2);
    }

    public function getOrder(): int
    {
        return 3;
    }
}
