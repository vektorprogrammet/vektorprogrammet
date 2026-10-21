<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\Fixture as AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use AppBundle\Entity\Feedback;

class LoadFeedbackData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $f = new Feedback();
        $f->setTitle('Hvordan stille flere spørsmål?')
        ->setDescription('Jeg vil gjerne stille så mange spørsmål som overhodet mulig, hvordan gjør jeg det?')
        ->setType(Feedback::TYPE_QUESTION)
        ->setUser($this->getReference('user-team-member'));
        $manager->persist($f);
        $manager->flush();

        $this->setReference('feedback-1', $f);
    }
    public function getOrder(): int
    {
        return 5;
    }
}
