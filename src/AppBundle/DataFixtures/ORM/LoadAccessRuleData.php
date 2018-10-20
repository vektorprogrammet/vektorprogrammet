<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\AccessRule;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadAccessRuleData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $rule = new AccessRule();
        $rule->setName("All departments");
        $rule->setResource("all_departments");
        $rule->setMethod('GET');
        $rule->setForExecutiveBoard(true);

        $manager->persist($rule);
        
        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}
