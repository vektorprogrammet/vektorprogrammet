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
        $parentCourse1 = new ParentCourse();

        $parentCourse1->setSpeaker('Alexander J Ohrt');
        $parentCourse1->setPlace('R5, Gløshaugen');
        $parentCourse1->setLink('https://use.mazemap.com/#v=1&zlevel=-1&center=10.405274,63.415645&zoom=18&campusid=1&typepois=7&sharepoitype=poi&sharepoi=1949');
        $parentCourse1->setDate(new \DateTime());
        $parentCourse1->setInformation('Dette blir et kurs i hvordan overleve de 2 første semestrene på MTFYMA! Åpent for alle kjære Nablakomponenter!');

        $manager->persist($parentCourse1);

        $parentCourse2 = new ParentCourse();
        $parentCourse2->setSpeaker('Sivert Lundli');
        $parentCourse2->setPlace('S6, Gløshaugen');
        $parentCourse2->setLink('https://use.mazemap.com/#v=1&zlevel=1&center=10.403405,63.417740&zoom=18&campusid=1&typepois=7&sharepoitype=poi&sharepoi=1247');
        $parentCourse2->setDate(new \DateTime());
        $parentCourse2->setInformation('Dette blir et kurs i hvordan overleve de 2 første semestrene på Komtek! Åpent for alle kjære Kom-tekere!');

        $manager->persist($parentCourse2);

        $parentCourse3 = new ParentCourse();
        $parentCourse3->setSpeaker('Eivind Kopperud');
        $parentCourse3->setPlace('F1, Gløshaugen');
        $parentCourse3->setLink(' https://use.mazemap.com/#v=1&zlevel=1&center=10.403268,63.416539&zoom=18&campusid=1&typepois=7&sharepoitype=poi&sharepoi=1000134578');
        $parentCourse3->setDate(new \DateTime());
        $parentCourse3->setInformation('20/21 sitt første foreldremøte! Det blir servert marie-kjeks og First-Price-brus, til alle deltakeres store glede! Lær om pedagogikk knyttet til undervisning av matematikk til andre. Velkommen! Velbekomme!');

        $manager->persist($parentCourse3);

        $manager->flush();

        $this->setReference('parent-course-1', $parentCourse1);
        $this->setReference('parent-course-2', $parentCourse2);
        $this->setReference('parent-course-3', $parentCourse3);
        $this->setReference('parent-course-3', $parentCourse3);
    }
}
