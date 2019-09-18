<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Team;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadTeamData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $team1 = new Team();
        $team1->setDepartment($this->getReference('dep-1'));
        $team1->setName('Styret');
        $team1->setEmail('styret@vektorprogrammet.no');
        $team1->setAcceptApplication(true);
        $team1->setShortDescription('Dette er styret. Det er vi som tror vi bestemmer alt! Vi passer på at alle gjør som de blir bedt om ellers vanker kakestraff.');
        $team1->setDescription('<div class="large-7 columns team-description-column">
            <h2>IT i Vektorprogrammet</h2>

<p>Siden oppstarten har Vektorprogrammet opplevd en <strong>kraftig vekst</strong> i antall vektorassistenter, ungdomsskoler som deltar i programmet og teammedlemmer som jobber med å drive Vektorprogrammet. Denne veksten fører med seg en del <strong>utfordringer </strong>knyttet til organisering av Vektorprogrammet, og vi i IT-teamet jobber kontinuerlig med å hjelpe organisasjonen til å fungere på best mulig måte.</p>

<p>Vi jobber for alle avdelinger i Norge, men vi er lokalisert i Trondheim. Det betyr at vi får være med på alle <strong>sosiale arragangementer</strong> som Vektorprogrammet NTNU arrangerer!</p>

<h3>Oppussing</h3>

<p>Høsten 2017 startet vi med en "extreme makover" av nettsiden. Her får du muligheten til å være med på å utforme og utvikle fremtidens vektorprogrammet.no helt fra starten av!</p>

<p><img alt="" src="/images/IT.jpg"><br>
<strong><em><small>Vi er en trivelig gjeng</small></em></strong></p>

<hr>
<h3>Opptak</h3>

<p>IT-teamet er alltid på jakt etter nye medlemmer, og har du erfaring med minst én av følgene&nbsp;oppfordres du til å sende oss en <strong>søknad</strong>!</p>

<ul>
	<li>Grafisk design for web</li>
	<li>UI/UX-design</li>
	<li>Frontend utvikling</li>
	<li>Backend utvikling</li>
	<li>Testing</li>
	<li>Serveradministrering (Ubuntu)</li>
</ul>

<h3>I IT teamet ser vi etter følgende egenskaper</h3>
<table>
    <thead>
        <tr>
            <th>Egenskap</th>
            <th>Viktighet</th>
            <th>Poeng</th>
</tr>
    </thead>
    <tbody>
        <tr>
            <td>Kodeferdighet</td>
            <td>Veldig viktig</td>
            <td>4</td>
        </tr>
        <tr>
            <td>Intellij hax</td>
            <td>Sykt viktig</td>
            <td>1000</td>
        </tr>
    </tbody>
</table>

<p>&nbsp;</p>

<p>Søknadsfrist: <strong>11. februar</strong></p>
        </div>');
        $manager->persist($team1);

        $team2 = new Team();
        $team2->setDepartment($this->getReference('dep-1'));
        $team2->setName('IT');
        $team2->setEmail('IT@vektorprogrammet.no');
        $team2->setShortDescription('Det er vi som driver med IT');
        $team2->setAcceptApplication(true);
        $team2->setDeadline(new \DateTime('now +3 days'));
        $manager->persist($team2);

        $team3 = new Team();
        $team3->setDepartment($this->getReference('dep-1'));
        $team3->setName('Rekruttering');
        $team3->setEmail('rekruttering@vektorprogrammet.no');
        $team3->setShortDescription('Rekruttering');
        $team3->setAcceptApplication(true);
        $manager->persist($team3);

        $team4 = new Team();
        $team4->setDepartment($this->getReference('dep-1'));
        $team4->setName('Sponsor (Samarbeidskoordinatorer)');
        $team4->setEmail('sponsor@vektorprogrammet.no');
        $team4->setShortDescription('Sponsor');
        $team4->setAcceptApplication(true);
        $manager->persist($team4);

        $team5 = new Team();
        $team5->setDepartment($this->getReference('dep-1'));
        $team5->setName('Evaluering');
        $team5->setShortDescription('Evaluering');
        $team5->setAcceptApplication(false);
        $manager->persist($team5);

        $team6 = new Team();
        $team6->setDepartment($this->getReference('dep-1'));
        $team6->setName('Eksport');
        $team6->setShortDescription('Eksport');
        $team6->setAcceptApplication(false);
        $team6->setActive(false);
        $manager->persist($team6);

        $team7 = new Team();
        $team7->setDepartment($this->getReference('dep-1'));
        $team7->setName('Skolekoordinering');
        $team7->setShortDescription('Skolekoordinering');
        $team7->setAcceptApplication(false);
        $team7->setEmail('skolekoordinering.ntnu@vektorprogrammet.no');
        $manager->persist($team7);

        $team2 = new Team();
        $team2->setDepartment($this->getReference('dep-2'));
        $team2->setName('Sponsor (Samarbeidskoordinatorer)');
        $manager->persist($team2);

        $team2 = new Team();
        $team2->setDepartment($this->getReference('dep-2'));
        $team2->setName('Sponsor (Samarbeidskoordinatorer)');
        $manager->persist($team2);

        $team2 = new Team();
        $team2->setDepartment($this->getReference('dep-2'));
        $team2->setName('Rekruttering');
        $manager->persist($team2);

        $team2 = new Team();
        $team2->setDepartment($this->getReference('dep-3'));
        $team2->setName('Sponsor (Samarbeidskoordinatorer   )');
        $manager->persist($team2);

        $team2 = new Team();
        $team2->setDepartment($this->getReference('dep-3'));
        $team2->setName('IT');
        $manager->persist($team2);

        $team2 = new Team();
        $team2->setDepartment($this->getReference('dep-4'));
        $team2->setName('TEAMET');
        $manager->persist($team2);

        $manager->flush();

        $this->addReference('team-1', $team1);
        $this->addReference('team-2', $team2);
    }

    public function getOrder()
    {
        return 3;
    }
}
