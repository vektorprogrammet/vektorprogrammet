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
            'vektor_i_media' => '<p>Vektorprogrammet i media:</p><p>VG: Studenter hjelper elever med matte</p><p>NRK: Studenter hjelper elever med matte</p><p>DAGBLADET: Studenter hjelper elever med matte</p><p>AFTENPOSTEN: Studenter hjelper elever med matte</p>',
            'about-header' => '<h1>Om Vektorprogrammet</h1><p>Vektorprogrammet arbeider for &aring; &oslash;ke interessen for matematikk og realfag blant elever i grunnskolen. Vi er en nasjonal studentorganisasjon som sender studenter med god realfagskompetanse til skoler for &aring; hjelpe elevene i matematikktimene. Disse studentene har ogs&aring; gode pedagogiske evner og er gode rollemodeller &ndash;&nbsp;<strong>de er Norges realfagshelter.</strong></p>',
            'about-motivate-children' => '<h2>Motivere elever</h2><p>Vektorprogrammet &oslash;nsker &aring; &oslash;ke matematikkforst&aring;elsen blant elever i grunnskolen. Forst&aring;else gir mestringsf&oslash;lelse som f&oslash;rer til videre motivasjon. Siden matematikk er grunnlaget for alle realfag er m&aring;let at dette ogs&aring; skal f&oslash;re til motivasjon og videre utforskning av realfagene.</p>',
            'about-motivate-students' => '<h2>Motivere studenter</h2><p>Vi har som m&aring;l at alle studentene skal sitte igjen mer motivert for videre studier etter &aring; ha v&aelig;rt vektorassistent. Av erfaring vet vi at muligheten til &aring; formidle egen kunnskap og se at deres arbeid gir elevene mestringsf&oslash;lelse er en sterk motivasjonsfaktor. Videre arrangerer vi b&aring;de sosiale og faglige arrangementer for &aring; forsterke denne motivasjonen.</p>',
            'about-try-teaching' => '<h2>En forsmak til l&aelig;reryrket!</h2><p>Siden studentene er tilstede i undervisningen f&aring;r de en introduksjon til l&aelig;reryrket. Mange som studerer realfag vurderer en fremtid som l&aelig;rer, og f&aring;r gjennom oss muligheten til &aring; f&aring; reell erfaring.</p>',
            'about-faq' => '<h2>Ofte stilte sp&oslash;rsm&aring;l og svar</h2><h5>Er verv i Vektorprogrammet betalt?</h5><p>Vektorprogrammet er en frivillig, studentdrevet organisasjon, der alle medlemmer jobber som frivillige. Du f&aring;r delta p&aring; sosiale arrangementer og muligheten til &aring; p&aring;virke en organisasjon som er den st&oslash;rste av sitt slag.</p><h5>Hvor stor er arbeidsmengden for en vektorassistent?</h5><p>Enkel stilling tilsvarer 4 skoledager p&aring; en ungdomsskole, fordelt over 4 uker. Dobbel stilling tilsvarer 8 skoledager fordelt over &aring;tte uker. En vanlig skoledag er som regel fra 08:00-14:00, og i tillegg kommer transporttid.</p><h5>Kan jeg bestemme hvilken dag jeg vil v&aelig;re vektorassistent?</h5><p>Ja, p&aring; intervju blir du spurt om hvilke(n) dag(er) som passer for deg. Du vil ikke bli plassert p&aring; en dag du har sagt at du ikke kan.</p><h5>F&aring;r jeg en attest dersom jeg har v&aelig;rt vektorassistent?</h5><p>Ja, det gj&oslash;r du. Disse deles normalt ut p&aring; avslutningen for vektorassistentene det tilh&oslash;rende semesteret, men dersom du av en eller annen grunn ikke kunne delta, ta kontakt med ditt lokalstyre via mail. Vil du ha attest for tidligere semester? Ta ogs&aring; kontakt med ditt lokalstyre p&aring; mail.</p><h5>Hva gj&oslash;r jeg dersom klassen jeg skal v&aelig;re i ikke er tilstede eller dersom det er feil i timeplanen jeg har f&aring;tt fra Vektorprogrammet?</h5><p>Ta kontakt med din skolekoordinator og forklar situasjonen. Dette er den personen du fikk skoledokumentene av.</p><h5>F&aring;r jeg noen ekstra utgifter av &aring; v&aelig;re vektorassistent?</h5><p>Nei, Vektorprogrammet dekker bussbilletter, t-skjorte og pedagogikk-kurs.</p><h5>Hva er pedagogikk-kurs?</h5><p>Alle nye vektorassistenter m&aring; delta p&aring; et pedagogikk-kurs i starten av semesteret for &aring; f&aring; en enkel innf&oslash;ring i hvordan man kan l&aelig;re bort til andre p&aring; en god m&aring;te.</p><h5>Jeg har v&aelig;rt vektorassistent f&oslash;r, m&aring; jeg p&aring; intervju eller pedagogikk-kurs igjen?</h5><p>Nei, alle tidligere vektorassistenter trenger kun &aring; s&oslash;ke via nettsiden. For &aring; f&aring; eventuelle bussbilletter m&aring; du m&oslash;te opp p&aring; slutten av pedagogikk-kurset.</p><h5>Hvordan s&oslash;ker jeg team?</h5><p>G&aring; inn p&aring; denne siden&nbsp;<a href="http://localhost:8000/team">her</a>, finn et eller flere team du er interessert i. Hvis dette teamet tar opp nye medlemmer vil det v&aelig;re en knapp hvor du kan s&oslash;ke. Hvis det ikke er opptak kan du sende en mail til teamleder og si ifra at du er interessert.</p><h5>Hva er forskjellen p&aring; vektorassistent og teammedlem?</h5><p>Som vektorassistent vil man reise til ungdomsskolen som l&aelig;rerassistent, mens som teammedlem er man med p&aring; &aring; p&aring;virke Vektorprogrammet som organisasjon. Som teammedlem blir man alts&aring; med i administrasjonen, og arbeidsoppgavene avhenger av hvilket team man er med i.</p><h5>G&aring;r det an &aring; b&aring;de v&aelig;re vektorassistent og med i team samtidig?</h5><p>Det er fullt mulig &aring; v&aelig;re begge deler samtidig. Som vektorassistent vil man kun bli sendt ut 4 eller 8 ganger per semester. Dette gj&oslash;r at arbeidsmengden er overkommelig, og kan fint kombineres med teamarbeid og studier.</p><h5>Jeg glemte s&oslash;knadsfristen. Hva gj&oslash;r jeg n&aring;?</h5><p>G&aring; til &#39;Kontakt&#39; i menyen og velg din region. Der finnes det ett kontaktskjema som du kan fylle ut, s&aring; finner vi ut av det sammen.</p><h5>I hvilke regioner holder Vektorprogrammet til?</h5><p>Trondheim, Oslo, Bergen og &Aring;s</p><h5>Kan jeg v&aelig;re vektorassistent flere semestre?</h5><p>Ja, men du m&aring; huske &aring; s&oslash;ke p&aring; nytt hvert semester! Du trenger ikke g&aring; gjennom intervju og pedagogikk-kurs p&aring; nytt.</p><p>Noe du lurer p&aring;?&nbsp;<strong><a href="http://localhost:8000/kontakt">Ta kontakt med oss!</a></strong></p>',
            'teacher-header' => '<h1>Vektorassistenter i skolen</h1><p>Vektorprogrammet er en frivillig organisasjon som tilbyr ungdomsskoler i Norge hjelpel&aelig;rere i matematikktimene. Her kan du enkelt s&oslash;ke om &aring; f&aring; studenter til &aring; hjelpe og motivere elevene i dine timer.</p>',
            'teacher-assistants-in-class' => '<h2>Vektorassistenter i matteundervisning</h2><p>Vektorprogrammet er Norges st&oslash;rste organisasjon som arbeider for &aring; &oslash;ke interessen for matematikk og realfag blant elever i grunnskolen. Vi er en studentorganisasjon som sender ut dyktige og motiverte studenter til ungdomsskoler i norge.</p><p>Studentene fungerer som l&aelig;rerens assistenter og kan dermed bidra til at elevene raskere f&aring;r hjelp i timen, og at undervisningen blir mer tilpasset de ulike elevgruppene. Vi har erfart at l&aelig;rerne ofte har mye &aring; gj&oslash;re i timene, og ofte ikke rekker &aring; hjelpe alle elevene som st&aring;r fast. Derfor er vi overbevist om at to assistenter kan forhindre mye hodebry for b&aring;de l&aelig;rere og elever.&nbsp;<br />Hvert &aring;r gjennomf&oslash;rer vi evalueringsunders&oslash;kelser, og i gjennomsnitt sier over 95% av l&aelig;rerne at de er forn&oslash;yde med prosjektet og &oslash;nsker &aring; delta videre.</p><p>Alle assistentene har v&aelig;rt gjennom en intervjuprossess som gj&oslash;r oss sikre p&aring; at de vil passe som assistentl&aelig;rere og kan v&aelig;re gode forbilder for elevene. Dette er en unik mulighet til &aring; f&aring; inn rollemodeller i klasserommet som kan v&aelig;re med &aring; gi elevene mer motivasjon.</p>',
            'teacher-how-to-use' => '<h2>Enkelt &aring; bruke assistenter i undervisningen</h2><p>Assistentene kan brukes som hjelp i undervisningen. Her er noen forslag vi har gode erfaringer med:</p><ul><li>Hjelpe til med oppgavel&oslash;sing i klasserom</li><li>Utfordre de sterkeste elevene med vanskeligere oppgaver</li><li>Engasjere elever med matteleker, g&aring;ter og spill</li><li>Arbeid med elever p&aring; grupperom</li></ul>',
            'teams-header' => '<h1>Styre og team</h1><p>Vektorprogrammet er en stor organisasjon med assistenter i 4 norske byer. Vi trenger derfor mange frivillige bak kulissene som kan f&aring; hjulene til &aring; g&aring; rundt. Uten vektorprogrammets 13 team hadde dette aldri g&aring;tt ann!</p><p>Kunne du tenkt deg et team-verv hos oss?&nbsp;<br /><strong><strong>Les mer om de ulike teamene nedenfor!</strong></strong></p>',
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
