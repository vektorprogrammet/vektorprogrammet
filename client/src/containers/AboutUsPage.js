import React, { Component } from 'react';
import { Grid } from 'semantic-ui-react';
import { Link } from 'react-router-dom';
import './AboutUsPage.css';
import ContactUsPopUp from '../components/ContactUsPopUp';
import Faq from '../components/Faq';
import MainTextAboutUs from '../components/MainTextAboutUs';
import PageHeader from '../components/PageHeader';


export default () => {
  const faqQuestionsAndAnswers = [
    {
      question: 'Er verv i Vektorprogrammet betalt?',
      answer: 'Nei, Vektorprogrammet er en frivillig, studentdrevet organisasjon, der ingen verv er betalt i form av norske kroner. Du får derimot delta på sosiale arrangementer og muligheten til å påvirke en organisasjon som er den største av sitt slag.',
    },
    {
      question: 'Hvor stor er arbeidsmengden for en vektorassistent?',
      answer: 'Enkel stilling tilsvarer 4 skoledager på en ungdomsskole, fordelt over 4 uker. Dobbel stilling tilsvarer 8 skoledager fordelt over åtte uker. En vanlig skoledag er som regel fra 08:00-14:00, og i tillegg kommer transporttid.',
    },
    {
      question: 'Kan jeg bestemme hvilken dag jeg vil være vektorassistent?',
      answer: 'Ja, på intervju blir du spurt om hvilke(n) dag(er) som passer for deg. Du vil ikke bli plassert på en dag du har sagt at du ikke kan.',
    },
    {
      question: 'Får jeg en attest dersom jeg har vært vektorassistent?',
      answer: 'Ja, det gjør du. Disse deles normalt ut på avslutningen for vektorassistentene det tilhørende semesteret, men dersom du av en eller annen grunn ikke kunne delta, ta kontakt med ditt lokalstyre via mail. Vil du ha attest for tidligere semester? Ta også kontakt med ditt lokalstyre på mail.',
    },
    {
      question: 'Hva gjør jeg dersom klassen jeg skal være i ikke er tilstede eller dersom det er feil i timeplanen jeg har fått fra Vektorprogrammet?',
      answer: 'Ta kontakt med din skolekoordinator og forklar situasjonen. Dette er den personen du fikk skoledokumentene av.',
    },
    {
      question: 'Får jeg noen ekstra utgifter av å være vektorassistent?',
      answer: 'Nei, Vektorprogrammet dekker bussbilletter, t-skjorte og pedagogikk-kurs.',
    },
    {
      question: 'Hva er pedagogikk-kurs?',
      answer: 'Alle nye vektorassistenter må delta på et pedagogikk-kurs i starten av semesteret for å få en enkel innføring i hvordan man kan lære bort til andre på en god måte.',
    },
    {
      question: 'Jeg har vært vektorassistent før, må jeg på intervju eller pedagogikk-kurs igjen?',
      answer: 'Nei, alle tidligere vektorassistenter trenger kun å søke via nettsiden. For å få eventuelle bussbilletter må du møte opp på slutten av pedagogikk-kurset.',
    },
    {
      question: 'Hvordan søker jeg team?',
      answer: 'Gå inn på denne siden her, finn et eller flere team du er interessert i. Hvis dette teamet tar opp nye medlemmer vil det være en knapp hvor du kan søke. Hvis det ikke er opptak kan du sende en mail til teamleder og si ifra at du er interessert.',
    },
    {
      question: 'Hva er forskjellen på vektorassistent og teammedlem?',
      answer: 'Som vektorassistent vil man reise til ungdomsskolen som lærerassistent, mens som teammedlem er man med på å påvirke Vektorprogrammet som organisasjon. Som teammedlem blir man altså med i administrasjonen, og arbeidsoppgavene avhenger av hvilket team man er med i.',
    },
    {
      question: 'Går det an å både være vektorassistent og med i team samtidig?',
      answer: 'Det er fullt mulig å være begge deler samtidig. Som vektorassistent vil man kun bli sendt ut 4 eller 8 ganger per semester. Dette gjør at arbeidsmengden er overkommelig, og kan fint kombineres med teamarbeid og studier.',
    },
    {
      question: 'Jeg glemte søknadsfristen. Hva gjør jeg nå?',
      answer: 'Gå til \'Opptak & kontakt\' i menyen og velg din region. Der finnes det ett kontaktskjema som du kan fylle ut, så finner vi ut av det sammen.',
    },
    {question: 'I hvilke regioner holder Vektorprogrammet til?', answer: 'Trondheim, Oslo, Bergen, Ås og Tromsø'},
    {
      question: 'Kan jeg være vektorassistent flere semestre?',
      answer: 'Ja, men du må huske å søke på nytt hvert semester! Du trenger ikke gå gjennom intervju og pedagogikk-kurs på nytt.',
    },
    /*TODO: Fix question below so it fits with new web-page*/
    {
      question: 'Finner du ikke svar på det du lurer på?',
      answer: 'Gå til \'Opptak & kontakt\' i menyen og velg din region. Der finnes det et kontaktskjema nederst som du kan fylle ut, så skal vi svare så raskt vi kan!',
    },

  ];
  return (
    <div className="about-us-page">
      <div className="container">
        <PageHeader>
          <h1>Om Vektorprogrammet</h1>
          <p>
            Vektorprogrammet arbeider for å øke interessen for matematikk og realfag blant elever i grunnskolen. Vi
            er en nasjonal studentorganisasjon som sender studenter med utmerket realfagskompetanse til skoler for å
            hjelpe elevene i matematikktimene. Disse studentene har også gode pedagogiske evner og er gode
            rollemodeller – <span className={'medium-bold'}>de er Norges realfagshelter.</span>
          </p>
        </PageHeader>
      </div>
      <Grid columns={3} padded>
        <Grid.Column width={16}>
          <div className={'about-us-main'}>
            <Grid.Row>
              <MainTextAboutUs/>
            </Grid.Row>
            <Grid.Row>
              <div id={'FAQ'} className="aboutUs-FAQ">
                <Faq questionsAndAnswers={faqQuestionsAndAnswers}/>
              </div>
            </Grid.Row>
            <Grid.Row>
              <div id={'kontakt-info'} className="contact-info">
                <p>Noe du lurer på? <Link to={'/kontakt'}>Ta kontakt med oss!</Link></p>
              </div>
            </Grid.Row>
          </div>
        </Grid.Column>
      </Grid>
    </div>
  );
}
