<?php

namespace App\Support\DataFixtures\ORM;

use App\Entity\ChangeLogItem;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LoadChangeLogData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Get the order of this fixture.
     */
    public function getOrder(): int
    {
        return 1;
    }

    /**
     * Load data fixtures with the passed EntityManager.
     *
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        $changeLogItem = new ChangeLogItem();
        $changeLogItem->setTitle('Automatisk invitasjon til Slack for nye');
        $changeLogItem->setDescription('Når et nytt medlem blir lagt til i team får han/hun tilgang til sin vektorprogrammet.no-epost ved å logge inn på gmail.com med sin bruker på vektorprogrammet.no. Når det nye medlemmet logger inn på Gmail for første gang vil det ligge en invitasjon til Slack der.');
        $changeLogItem->setGithubLink('https://github.com');
        $changeLogItem->setDate(new \DateTime());

        $manager->persist($changeLogItem);

        $changeLogItem = new ChangeLogItem();
        $changeLogItem->setTitle('Changelog-widget lagt til kontrollpanel');
        $changeLogItem->setDescription('Changelog-widgeten er ferdig, og kan tas i bruk. Den kan brukes til å holde styr på endringer IT-teamet gjør. Klikk deg videre i kontrollpanel-menyen på venstre side for å opprette en ny endring. Denne vil deretter bli synlig i widgeten.  ');
        $changeLogItem->setGithubLink('https://github.com/vektorprogrammet/vektorprogrammet/tree/master/app');
        $changeLogItem->setDate(new \DateTime());

        $manager->persist($changeLogItem);

        $changeLogItem = new ChangeLogItem();
        $changeLogItem->setTitle('Påmelding til foreldrekurs. ');
        $changeLogItem->setDescription('På nettsiden med informasjon til foreldre finnes det en lenke til påmelding til foreldrekurs!');
        $changeLogItem->setGithubLink('https://github.com/vektorprogrammet/vektorprogrammet/tree/parent-registration/src/AppBundle/Controller');
        $changeLogItem->setDate(new \DateTime());

        $manager->persist($changeLogItem);

        $changeLogItem = new ChangeLogItem();
        $changeLogItem->setTitle('Informasjon til foreldre.');
        $changeLogItem->setDescription('Det er nå blitt lagt til en nettside med informasjon til foreldre om Vektorprogrammet. Denne kan man klikke seg inn på via hovedmenyen.');
        $changeLogItem->setGithubLink('https://github.com/vektorprogrammet/vektorprogrammet/tree/Parents-Info/src/AppBundle/Controller');
        $changeLogItem->setDate(new \DateTime());

        $manager->persist($changeLogItem);

        $changeLogItem = new ChangeLogItem();
        $changeLogItem->setTitle('Man kan nå endre sin medintervjuer!');
        $changeLogItem->setDescription('Du kan nå gå inn i kontrollpanelet, og endre på din medintervjuer.');
        $changeLogItem->setGithubLink('https://github.com/vektorprogrammet/vektorprogrammet/tree/edit_co_interviewer');
        $changeLogItem->setDate(new \DateTime());

        $manager->persist($changeLogItem);

        $changeLogItem = new ChangeLogItem();
        $changeLogItem->setTitle('Søknadsfrist på team er optional!');
        $changeLogItem->setDescription('Søknadsfristen er ikke helt statisk lenger');
        $changeLogItem->setGithubLink('https://github.com/vektorprogrammet/vektorprogrammet');
        $changeLogItem->setDate(new \DateTime());

        $manager->persist($changeLogItem);

        $changeLogItem = new ChangeLogItem();
        $changeLogItem->setTitle('Quicklinks i kontrollpanelet!');
        $changeLogItem->setDescription('Fra og med nå kan man legge til quicklinks til andre steder via sitt eget kontrollpanel. Dermed vil man raskere kunne navigere mellom ofte brukte sider, i stedet for å trykke seg gjennom mange sider hver gang.');
        $changeLogItem->setGithubLink('https://github.com/vektorprogrammet/vektorprogrammet/tree/quicklinks');
        $changeLogItem->setDate(new \DateTime());

        $manager->persist($changeLogItem);

        $manager->flush();
    }
}
