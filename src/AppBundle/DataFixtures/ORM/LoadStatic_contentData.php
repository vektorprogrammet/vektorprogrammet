<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\StaticContent;


class LoadStatic_contentData extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $elements = array(
            'infobox_one' => 'Vektorprogrammet er Norges største organisasjon som jobber for å øke interessen for matematikk og realfag blant elever i grunnskolen. Vi sender realfagssterke studenter til barne- og ungdomsskoler hvor de fungerer som lærerens assistent.',
            'infobox_two' => 'Dermed er det flere tilstede elevene kan spørre om hjelp, samtidig er studenten en rollemodell for elevene. På denne måten hjelper og inspirerer vi <i>alle</i> – ikke bare de som allerede er interessert i å lære matematikk og realfag!<br><br>',
            'banner_one' => '',
            'banner_two' => 'banner_two',
            'vektor_for_studenter' => 'Vektor for studenter',
            'vektor_for_bedrifter' => 'Vektor for bedrifter',
            'vektor_for_laerere' => 'Vektor for lærere',
            'vektor_i_media' => '<p>Vektorprogrammet i media:</p><p>VG: Studenter hjelper elever med matte</p><p>NRK: Studenter hjelper elever med matte</p><p>DAGBLADET: Studenter hjelper elever med matte</p><p>AFTENPOSTEN: Studenter hjelper elever med matte</p>',
            'omvektor' => '<h1>Om Vektorprogrammet</h1><p><strong>Vektorprogrammet arbeider for å øke interessen for matematikk og realfag blant elever i grunnskolen. Vi er en nasjonal studentorganisasjon som sender studenter med utmerket realfagskompetanse til skoler for å hjelpe elevene i matematikktimene. Disse studentene har også gode pedagogiske evner og er gode rollemodeller – de er Norges realfagshelter.</strong></p>
                            <h1>Mål og verdier</h1><li>Vektorprogrammet ønsker å øke matematikkforståelsen blant elever i grunnskolen. Forståelse gir mestringsfølelse som fører til videre motivasjon. Siden matematikk er grunnlaget for alle realfag er målet at dette også skal føre til motivasjon og videre utforskning av realfagene.</li>
                            <h1>Historie</h1><p>Vektorprogrammet er et initiativ som ble startet av linjeforeningen Nabla i september 2010 og hadde den gang det kanskje noe mindre fengende navnet «Nablas initativ for hjelp i skolen».</p>',
            'businessesinfo' => '<h1>For bedrifter</h1><p>Vektorprogrammet er en frivillig studentorganisasjon som sender studenter fra realfag</p>',
            'contactinfo_businesses' => '<h1>Kontakt</h1><strong>Generelle henvendelser rettes til:</strong>',
            'contactinfo_schools' => '<h1>Kontakt</h1><strong>Generelle henvendelser rettes til:</strong>',
            'schoolinfo' => '<h1>For skoler</h1><p>kunne drive Vektorprogrammet er det helt essensielt med samarbeidsavtaler med skoler...</p>',
            'contactinfo' => '<h1>Kontakt</h1><strong>Generelle henvendelser rettes til:</strong><p>studenter@vektorprogrammet.no</p>',
            'studentinfo' => '<h1>For studenter</h1><p>kunne drive Vektorprogrammet er det helt essensielt med samarbeidsavtaler med skoler...</p>',
        );
        foreach ($elements as $html_id => $content) {
            $staticElement = new StaticContent();
            $staticElement->setHtmlId($html_id);
            $staticElement->setHtml($content);

            $manager->persist($staticElement);
        }
        $manager->flush();
    }
}