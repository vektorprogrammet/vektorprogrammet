<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\InterviewQuestion;

class LoadInterviewQuestionData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $question1 = new InterviewQuestion();
        $question1->setQuestion('Hva er din motivasjon for å søke vektorassistent?');
        $question1->setType('text');
        $manager->persist($question1);

        $question2 = new InterviewQuestion();
        $question2->setQuestion('Har du noen erfaring fra andre undervisningssituasjoner?');
        $question2->setType('text');
        $manager->persist($question2);

        $question3 = new InterviewQuestion();
        $question3->setQuestion('Har du andre studentverv?');
        $question3->setType('text');
        $manager->persist($question3);

        $question4 = new InterviewQuestion();
        $question4->setQuestion('Hva gjør deg godt egnet til jobben?');
        $question4->setType('text');
        $manager->persist($question4);

        $question5 = new InterviewQuestion();
        $question5->setQuestion('Hvilke egenskaper ser du etter i folk du samarbeider med?');
        $question5->setType('text');
        $manager->persist($question5);

        $question6 = new InterviewQuestion();
        $question6->setQuestion('Hvordan definerer du suksess? Har du et eksempel på noen du kjenner som har oppnådd suksess?');
        $question6->setType('text');
        $manager->persist($question6);

        $question7 = new InterviewQuestion();
        $question7->setQuestion('Hva skiller deg fra alle andre kvalifiserte søkere?');
        $question7->setType('text');
        $manager->persist($question7);

        $question8 = new InterviewQuestion();
        $question8->setQuestion('Kan du tenke deg å være med i organiseringen av Vektorprogrammet? Dette inneholder teamarbeid.');
        $question8->setType('text');
        $manager->persist($question8);

        $manager->flush();

        $this->setReference('iq-1', $question1);
        $this->setReference('iq-2', $question2);
        $this->setReference('iq-3', $question3);
        $this->setReference('iq-4', $question4);
        $this->setReference('iq-5', $question5);
        $this->setReference('iq-6', $question6);
        $this->setReference('iq-7', $question7);
        $this->setReference('iq-8', $question8);
    }

    public function getOrder()
    {
        return 1;
    }
}
