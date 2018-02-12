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
        $team1->setAcceptApplication(false);
        $team1->setShortDescription('Dette her styret. Det er vi som tror vi bestemmer alt! Vi passer på at alle gjør som de blir bedt om ellers vanker kakestraff');
        $team1->setDescription('<table border="1" cellpadding="1" cellspacing="1" style="width:500px">
	<tbody>
		<tr>
			<td>Dette er en tabell</td>
			<td>Med mange Rader</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>1234</td>
			<td>151231&oslash;l23mklg</td>
		</tr>
	</tbody>
</table>

<h1>STooor overskrift</h1>

<h3>Liten overskrift</h3>

<ul>
	<li>En liste</li>
	<li>Med mange</li>
	<li>Items</li>
</ul>

<p><a href="http://google.com">http://google.com</a></p>');
        $manager->persist($team1);

        $team2 = new Team();
        $team2->setDepartment($this->getReference('dep-1'));
        $team2->setName('IT');
        $team2->setShortDescription('Det er vi som driver med ID');
        $team2->setAcceptApplication(true);
        $manager->persist($team2);

        $team3 = new Team();
        $team3->setDepartment($this->getReference('dep-1'));
        $team3->setName('Rekruttering');
        $team3->setShortDescription('Rekruttering');
        $team3->setAcceptApplication(true);
        $manager->persist($team3);

        $team4 = new Team();
        $team4->setDepartment($this->getReference('dep-1'));
        $team4->setName('Sponsor');
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
        $team6->setAcceptApplication(true);
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
        $team2->setName('Samarbeidskoordinatorer');
        $manager->persist($team2);

        $team2 = new Team();
        $team2->setDepartment($this->getReference('dep-2'));
        $team2->setName('Sponsor');
        $manager->persist($team2);

        $team2 = new Team();
        $team2->setDepartment($this->getReference('dep-2'));
        $team2->setName('Rekruttering');
        $manager->persist($team2);

        $team2 = new Team();
        $team2->setDepartment($this->getReference('dep-3'));
        $team2->setName('Samarbeidskoordinatorer');
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
