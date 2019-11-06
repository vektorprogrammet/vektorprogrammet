<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\ParentCourse;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadParentCourseData extends AbstractFixture implements OrderedFixtureInterface
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
        $parentCourse = new ParentCourse();
        $parentCourse->setSpeaker('Alexander J Ohrt');
        $parentCourse->setPlace('R5, Gløshaugen');
        $parentCourse->setDate(new \DateTime());
        $parentCourse->setInformation('Dette blir et kurs i hvordan overleve de 2 første semestrene på MTFYMA! Åpent for alle kjære Nablakomponenter!');

        $manager->persist($parentCourse);

        $parentCourse = new ParentCourse();
        $parentCourse->setSpeaker('Sivert Lundli');
        $parentCourse->setPlace('S6, Gløshaugen');
        $parentCourse->setDate(new \DateTime());
        $parentCourse->setInformation('Dette blir et kurs i hvordan overleve de 2 første semestrene på Komtek! Åpent for alle kjære Kom-tekere!');

        $manager->persist($parentCourse);

        $parentCourse = new ParentCourse();
        $parentCourse->setSpeaker('Eiving Kopperud');
        $parentCourse->setPlace('F1, Gløshaugen');
        $parentCourse->setDate(new \DateTime());
        $parentCourse->setInformation('20/21 sitt første foreldremøte! Det blir servert marie-kjeks og First-Price-brus, til alle deltakeres store glede! Lær om pedagogikk knyttet til undervisning av matematikk til andre. Velkommen! Velbekomme!');

        $manager->persist($parentCourse);

        $manager->flush();
    }
}
