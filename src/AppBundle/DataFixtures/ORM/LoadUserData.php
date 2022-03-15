<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $user1 = new User();
        $user1->setActive('1');
        $user1->setEmail('petter@stud.ntnu.no');
        $user1->setCompanyEmail('petter@vektorprogrammet.no');
        $user1->setFirstName('Petter');
        $user1->setLastName('Johansen');
        $user1->setGender('0');
        $user1->setPhone('95347865');
        $user1->setUserName('petjo');
        $user1->setPassword('1234');
        $user1->addRole($this->getReference('role-4'));
        $user1->setFieldOfStudy($this->getReference('fos-1'));
        $user1->setPicturePath('images/profile1.jpg');
        $user1->setAccountNumber('1234.56.78903');
        $manager->persist($user1);

        $user2 = new User();
        $user2->setActive('1');
        $user2->setEmail('ida@stud.ntnu.no');
        $user2->setFirstName('Ida');
        $user2->setLastName('Andreassen');
        $user2->setGender('1');
        $user2->setPhone('95267841');
        $user2->setUserName('idaan');
        $user2->setPassword('1234');
        $user2->addRole($this->getReference('role-2'));
        $user2->setFieldOfStudy($this->getReference('fos-2'));
        $user2->setPicturePath('images/profile2.jpg');
        $user2->setAccountNumber('1234.56.78903');
        $manager->persist($user2);

        $user3 = new User();
        $user3->setActive('1');
        $user3->setEmail('kristoffer@stud.ntnu.no');
        $user3->setFirstName('Kristoffer');
        $user3->setLastName('Bø');
        $user3->setGender('0');
        $user3->setPhone('95148725');
        $user3->setUserName('kribo');
        $user3->setPassword('1234');
        $user3->addRole($this->getReference('role-1'));
        $user3->setFieldOfStudy($this->getReference('fos-3'));
        $user3->setPicturePath('images/profile3.jpg');
        $manager->persist($user3);

        $user4 = new User();
        $user4->setActive('1');
        $user4->setEmail('alm@mail.com');
        $user4->setFirstName('Thomas');
        $user4->setLastName('Alm');
        $user4->setGender('0');
        $user4->setPhone('12312312');
        $user4->setUserName('thomas');
        $user4->setPassword('123');
        $user4->addRole($this->getReference('role-2'));
        $user4->setFieldOfStudy($this->getReference('fos-1'));
        $user4->setPicturePath('images/profile4.jpg');
        $manager->persist($user4);

        $user5 = new User();
        $user5->setActive('1');
        $user5->setEmail('a@b.c');
        $user5->setFirstName('Reidun');
        $user5->setLastName('Persdatter Ødegaard');
        $user5->setGender('1');
        $user5->setPhone('92269548');
        $user5->setUserName('reidun');
        $user5->setPassword('123');
        $user5->addRole($this->getReference('role-4'));
        $user5->setFieldOfStudy($this->getReference('fos-1'));
        $user5->setPicturePath('images/profile5.jpg');
        $manager->persist($user5);

        $user6 = new User();
        $user6->setActive('1');
        $user6->setEmail('b@b.c');
        $user6->setFirstName('Siri');
        $user6->setLastName('Brenna Eskeland');
        $user6->setGender('1');
        $user6->setPhone('99540025');
        $user6->setUserName('siri');
        $user6->setPassword('123');
        $user6->addRole($this->getReference('role-4'));
        $user6->setFieldOfStudy($this->getReference('fos-1'));
        $user6->setPicturePath('images/defaultProfile.png');
        $manager->persist($user6);

        $user7 = new User();
        $user7->setActive('1');
        $user7->setEmail('c@b.c');
        $user7->setFirstName('Eirik');
        $user7->setLastName('Myrvoll-Nilsen');
        $user7->setGender('0');
        $user7->setPhone('93093824');
        $user7->setUserName('eirik');
        $user7->setPassword('123');
        $user7->addRole($this->getReference('role-3'));
        $user7->setFieldOfStudy($this->getReference('fos-1'));
        $user7->setPicturePath('images/defaultProfile.png');
        $manager->persist($user7);

        $user8 = new User();
        $user8->setActive('1');
        $user8->setEmail('d@b.c');
        $user8->setFirstName('Ruben');
        $user8->setLastName('Ravnå');
        $user8->setGender('0');
        $user8->setPhone('98059155');
        $user8->setUserName('ruben');
        $user8->setPassword('123');
        $user8->addRole($this->getReference('role-4'));
        $user8->setFieldOfStudy($this->getReference('fos-1'));
        $user8->setPicturePath('images/defaultProfile.png');
        $manager->persist($user8);

        $user9 = new User();
        $user9->setActive('1');
        $user9->setEmail('e@b.c');
        $user9->setFirstName('Liv');
        $user9->setLastName('Rasdal Håland');
        $user9->setGender('1');
        $user9->setPhone('45506381');
        $user9->setUserName('liv');
        $user9->setPassword('123');
        $user9->addRole($this->getReference('role-3'));
        $user9->setFieldOfStudy($this->getReference('fos-1'));
        $user9->setPicturePath('images/defaultProfile.png');
        $manager->persist($user9);

        $user10 = new User();
        $user10->setActive('1');
        $user10->setEmail('f@b.c');
        $user10->setFirstName('Johannes');
        $user10->setLastName('Bogen');
        $user10->setGender('0');
        $user10->setPhone('95480124');
        $user10->setUserName('johannes');
        $user10->setPassword('123');
        $user10->addRole($this->getReference('role-3'));
        $user10->setFieldOfStudy($this->getReference('fos-1'));
        $user10->setPicturePath('images/defaultProfile.png');
        $manager->persist($user10);

        $user11 = new User();
        $user11->setActive('1');
        $user11->setEmail('g@b.c');
        $user11->setFirstName('Cecilie');
        $user11->setLastName('Teisberg');
        $user11->setGender('1');
        $user11->setPhone('45688060');
        $user11->setUserName('cecilie');
        $user11->setPassword('123');
        $user11->addRole($this->getReference('role-3'));
        $user11->setFieldOfStudy($this->getReference('fos-1'));
        $user11->setPicturePath('images/defaultProfile.png');
        $manager->persist($user11);

        $user12 = new User();
        $user12->setActive('1');
        $user12->setEmail('h@b.c');
        $user12->setFirstName('Håkon');
        $user12->setLastName('Nøstvik');
        $user12->setGender('0');
        $user12->setPhone('99413718');
        $user12->setUserName('haakon');
        $user12->setPassword('123');
        $user12->addRole($this->getReference('role-3'));
        $user12->setFieldOfStudy($this->getReference('fos-1'));
        $user12->setPicturePath('images/defaultProfile.png');
        $manager->persist($user12);

        $user13 = new User();
        $user13->setActive('1');
        $user13->setEmail('i@b.c');
        $user13->setFirstName('Maulisha');
        $user13->setLastName('Thavarajan');
        $user13->setGender('1');
        $user13->setPhone('45439367');
        $user13->setUserName('maulisha');
        $user13->setPassword('123');
        $user13->addRole($this->getReference('role-3'));
        $user13->setFieldOfStudy($this->getReference('fos-4'));
        $user13->setPicturePath('images/defaultProfile.png');
        $manager->persist($user13);

        $user14 = new User();
        $user14->setActive('1');
        $user14->setEmail('ij@b.c');
        $user14->setFirstName('Åse');
        $user14->setLastName('Thavarajan');
        $user14->setGender('1');
        $user14->setPhone('45439369');
        $user14->setUserName('aase');
        $user14->setPassword('123');
        $user14->addRole($this->getReference('role-3'));
        $user14->setFieldOfStudy($this->getReference('fos-4'));
        $user14->setPicturePath('images/defaultProfile.png');
        $manager->persist($user14);

        $userInTeam1 = new User();
        $userInTeam1->setActive('1');
        $userInTeam1->setEmail('sortland@mail.com');
        $userInTeam1->setFirstName('Sondre');
        $userInTeam1->setLastName('Sortland');
        $userInTeam1->setGender('0');
        $userInTeam1->setPhone('12312312');
        $userInTeam1->setUserName('userInTeam1');
        $userInTeam1->setPassword('1234');
        $userInTeam1->addRole($this->getReference('role-2'));
        $userInTeam1->setFieldOfStudy($this->getReference('fos-1'));
        $userInTeam1->setPicturePath('images/sondre.jpg');
        $manager->persist($userInTeam1);

        $user = new User();
        $user->setActive('1');
        $user->setEmail('marte@mail.no');
        $user->setFirstName('Marte');
        $user->setLastName('Saghagen');
        $user->setGender('1');
        $user->setPhone('97623818');
        $user->setUserName('marte');
        $user->setPassword('123');
        $user->addRole($this->getReference('role-2'));
        $user->setFieldOfStudy($this->getReference('fos-1'));
        $user->setPicturePath('images/profile6.jpg');
        $manager->persist($user);
        $this->setReference('user-marte', $user);

        $user = new User();
        $user->setActive('1');
        $user->setEmail('anna@mail.no');
        $user->setFirstName('Anna');
        $user->setLastName('Madeleine Goldsack');
        $user->setGender('1');
        $user->setPhone('98896056');
        $user->setUserName('anna');
        $user->setPassword('123');
        $user->addRole($this->getReference('role-3'));
        $user->setFieldOfStudy($this->getReference('fos-1'));
        $user->setPicturePath('images/profile7.jpg');
        $manager->persist($user);
        $this->setReference('user-anna', $user);

        $user = new User();
        $user->setActive('1');
        $user->setEmail('angela@mail.no');
        $user->setFirstName('Angela');
        $user->setLastName('Maiken Johnsen');
        $user->setGender('1');
        $user->setPhone('91152489');
        $user->setUserName('angela');
        $user->setPassword('123');
        $user->addRole($this->getReference('role-1'));
        $user->setFieldOfStudy($this->getReference('fos-1'));
        $user->setPicturePath('images/defaultProfile.png');
        $manager->persist($user);
        $this->setReference('user-angela', $user);

        $user = new User();
        $user->setActive('0');
        $user->setEmail('inactive@mail.com');
        $user->setFirstName('Ina');
        $user->setLastName('Ktiv');
        $user->setGender('1');
        $user->setPhone('40404040');
        $user->setUserName('inactive');
        $user->setPassword('123');
        $user->addRole($this->getReference('role-1'));
        $user->setFieldOfStudy($this->getReference('fos-1'));
        $user->setPicturePath('images/defaultProfile.png');
        $manager->persist($user);
        $this->setReference('user-inactive', $user);
        
        $user10 = new User();
        $user10->setActive('1');
        $user10->setEmail('aaf@b.c');
        $user10->setFirstName('Kamilla');
        $user10->setLastName('Plaszko');
        $user10->setGender('1');
        $user10->setPhone('45484008');
        $user10->setUserName('kampla');
        $user10->setPassword('123');
        $user10->addRole($this->getReference('role-2'));
        $user10->setFieldOfStudy($this->getReference('fos-5'));
        $user10->setPicturePath('images/defaultProfile.png');
        $manager->persist($user10);

        $user11 = new User();
        $user11->setActive('1');
        $user11->setEmail('aag@b.c');
        $user11->setFirstName('Vuk');
        $user11->setLastName('Krivokapic');
        $user11->setGender('0');
        $user11->setPhone('47000000');
        $user11->setUserName('vuk');
        $user11->setPassword('123');
        $user11->addRole($this->getReference('role-3'));
        $user11->setFieldOfStudy($this->getReference('fos-3'));
        $user11->setPicturePath('images/defaultProfile.png');
        $manager->persist($user11);

        $user12 = new User();
        $user12->setActive('1');
        $user12->setEmail('aah@b.c');
        $user12->setFirstName('Markus');
        $user12->setLastName('Gundersen');
        $user12->setGender('0');
        $user12->setPhone('46000000');
        $user12->setUserName('markus');
        $user12->setPassword('123');
        $user12->addRole($this->getReference('role-3'));
        $user12->setFieldOfStudy($this->getReference('fos-1'));
        $user12->setPicturePath('images/defaultProfile.png');
        $manager->persist($user12);

        $user13 = new User();
        $user13->setActive('1');
        $user13->setEmail('aai@b.c');
        $user13->setFirstName('Erik');
        $user13->setLastName('Trondsen ');
        $user13->setGender('0');
        $user13->setPhone('45000000');
        $user13->addRole($this->getReference('role-2'));
        $user13->setFieldOfStudy($this->getReference('fos-1'));
        $user13->setPicturePath('images/defaultProfile.png');
        $manager->persist($user13);

        $userAssistant = new User();
        $userAssistant->setActive('1');
        $userAssistant->setEmail('assistant@gmail.com');
        $userAssistant->setFirstName('Assistent');
        $userAssistant->setLastName('Johansen');
        $userAssistant->setGender('0');
        $userAssistant->setPhone('47658937');
        $userAssistant->setUserName('assistent');
        $userAssistant->setPassword('1234');
        $userAssistant->addRole($this->getReference('role-1'));
        $userAssistant->setFieldOfStudy($this->getReference('fos-1'));
        $userAssistant->setPicturePath('images/defaultProfile.png');
        $userAssistant->setAccountNumber('1234.56.78903');
        $manager->persist($userAssistant);
        $this->setReference('user-assistant', $userAssistant);


        $userTeamMember = new User();
        $userTeamMember->setActive('1');
        $userTeamMember->setEmail('team@gmail.com');
        $userTeamMember->setFirstName('Team');
        $userTeamMember->setLastName('Johansen');
        $userTeamMember->setGender('0');
        $userTeamMember->setPhone('47658937');
        $userTeamMember->setUserName('teammember');
        $userTeamMember->setPassword('1234');
        $userTeamMember->addRole($this->getReference('role-2'));
        $userTeamMember->setFieldOfStudy($this->getReference('fos-1'));
        $userTeamMember->setPicturePath('images/defaultProfile.png');
        $userTeamMember->setAccountNumber('1234.56.78903');
        $manager->persist($userTeamMember);

        $user16 = new User();
        $user16->setActive('1');
        $user16->setEmail('nmbu@admin.no');
        $user16->setFirstName('Muhammed');
        $user16->setLastName('Thavarajan');
        $user16->setGender('1');
        $user16->setPhone('45439367');
        $user16->setUserName('nmbu');
        $user16->setPassword('1234');
        $user16->addRole($this->getReference('role-4'));
        $user16->setFieldOfStudy($this->getReference('fos-4'));
        $user16->setPicturePath('images/defaultProfile.png');
        $manager->persist($user16);

        $userTeamLeader = new User();
        $userTeamLeader->setActive('1');
        $userTeamLeader->setEmail('teamleader@gmail.com');
        $userTeamLeader->setFirstName('TeamLeader');
        $userTeamLeader->setLastName('Johansen');
        $userTeamLeader->setGender('0');
        $userTeamLeader->setPhone('47658937');
        $userTeamLeader->setUserName('teamleader');
        $userTeamLeader->setPassword('1234');
        $userTeamLeader->addRole($this->getReference('role-3'));
        $userTeamLeader->setFieldOfStudy($this->getReference('fos-1'));
        $userTeamLeader->setPicturePath('images/harold.jpg');
        $userTeamLeader->setAccountNumber('1234.56.78903');
        $manager->persist($userTeamLeader);

        $userAdmin = new User();
        $userAdmin->setActive('1');
        $userAdmin->setEmail('admin@gmail.com');
        $userAdmin->setFirstName('Admin');
        $userAdmin->setLastName('Johansen');
        $userAdmin->setGender('0');
        $userAdmin->setPhone('47658937');
        $userAdmin->setUserName('admin');
        $userAdmin->setPassword('1234');
        $userAdmin->addRole($this->getReference('role-4'));
        $userAdmin->setFieldOfStudy($this->getReference('fos-1'));
        $userAdmin->setPicturePath('images/harold.jpg');
        $userAdmin->setAccountNumber('1234.56.78903');
        $manager->persist($userAdmin);

        $user20 = new User();
        $user20->setActive('1');
        $user20->setEmail('jan-per-gustavio@gmail.com');
        $user20->setFirstName('Jan-Per-Gustavio');
        $user20->setLastName('Tacopedia');
        $user20->setGender('0');
        $user20->setPhone('81549300');
        $user20->setUserName('JanPerGustavio');
        $user20->setPassword('1234');
        $user20->addRole($this->getReference('role-3'));
        $user20->setFieldOfStudy($this->getReference('fos-3'));
        $user20->setPicturePath('images/defaultProfile.png');
        $manager->persist($user20);

        $user21 = new User();
        $user21->setActive('1');
        $user21->setEmail('seip@mail.com');
        $user21->setFirstName('Ingrid');
        $user21->setLastName('Seip Domben');
        $user21->setGender('1');
        $user21->setPhone('91104644');
        $user21->setUserName('ingrid');
        $user21->setPassword('123');
        $user21->addRole($this->getReference('role-1'));
        $user21->setFieldOfStudy($this->getReference('fos-1'));
        $user21->setPicturePath('images/defaultProfile.png');
        $manager->persist($user21);

        for ($i = 0; $i < 100; ++$i) {
            $user = new User();
            $user->setActive('0');
            $user->setEmail('scheduling-user-'.$i.'@mail.com');
            $user->setFirstName('scheduling-user-'.$i);
            $user->setLastName('user-lastName-'.$i);
            $user->setGender($i % 2 == 0 ? '0' : '1');
            $user->setPhone('12345678');
            $user->setUserName('scheduling-user-'.$i);
            $user->addRole($this->getReference('role-1'));
            $user->setFieldOfStudy($this->getReference('fos-1'));
            $user->setPicturePath('images/defaultProfile.png');
            $this->setReference('scheduling-user-'.$i, $user);
            $manager->persist($user);
        }


        for ($i = 0; $i < 50; ++$i) {
            $userAssistant = new User();
            $userAssistant->setActive('1');
            $userAssistant->setEmail('scheduling-assistant-'.$i.'@mail.com');
            $userAssistant->setFirstName('scheduling-assistant-'.$i);
            $userAssistant->setLastName('assistant-lastName-'.$i);
            $userAssistant->setGender($i % 2 == 0 ? '0' : '1');
            $userAssistant->setPhone((string)(10000000+$i));
            $userAssistant->setUserName('scheduling-assistant-'.$i);
            $userAssistant->addRole($this->getReference('role-1'));
            $userAssistant->setPicturePath('images/defaultProfile.png');
            $userAssistant->setFieldOfStudy($this->getReference('fos-1'));
            $userAssistant->setPassword('1234');


            $this->setReference('scheduling-assistant-'.$i, $userAssistant);
            $manager->persist($userAssistant);
        }




        $manager->flush();

        $this->setReference('user-1', $user1);
        $this->setReference('user-2', $user2);
        $this->setReference('user-3', $user3);
        $this->setReference('user-4', $user4);
        $this->setReference('user-8', $user8);
        $this->setReference('user-9', $user9);
        $this->setReference('user-10', $user10);
        $this->setReference('user-11', $user11);
        $this->setReference('user-12', $user12);
        $this->setReference('user-13', $user13);
        $this->setReference('user-16', $user16);
        $this->setReference('user-20', $user20);
        $this->setReference('user-21', $user21);
        $this->setReference('user-14', $user14);
        $this->setReference('userInTeam1', $userInTeam1);
        $this->setReference('user-team-member', $userTeamMember);
        $this->setReference('user-team-leader', $userTeamLeader);
        $this->setReference('user-admin', $userAdmin);
    }

    public function getOrder()
    {
        return 4;
    }
}
