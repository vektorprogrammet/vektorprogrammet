<?php

namespace App\DataFixtures\ORM;

use App\Entity\Feedback;
use App\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LoadFeedbackData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $f = new Feedback();
        $f->setTitle('Hvordan stille flere spørsmål?')
        ->setDescription('Jeg vil gjerne stille så mange spørsmål som overhodet mulig, hvordan gjør jeg det?')
        ->setType(Feedback::TYPE_QUESTION)
        ->setUser($this->getReference('user-team-member', User::class));
        $manager->persist($f);
        $manager->flush();

        $this->setReference('feedback-1', $f);
    }

    public function getOrder(): int
    {
        return 5;
    }
}
