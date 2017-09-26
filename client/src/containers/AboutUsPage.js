import React, {Component} from 'react';
/*import { Link } from 'react-router-dom';*/
import { Grid } from 'semantic-ui-react';
import './AboutUsPage.css';

class AboutUsPage extends Component {
    render() {
        return (
            <Grid divided='vertically'>
                <Grid.Row columns={2}>
                    <Grid.Column width={10}>
                        <p className="about-us-text" style={{marginTop: -4}}>
                            Vektorprogrammet arbeider for å øke interessen for matematikk og realfag blant elever i grunnskolen. Vi er en nasjonal studentorganisasjon som sender studenter med utmerket
                            realfagskompetanse til skoler for å hjelpe elevene i matematikktimene. Disse studentene har også gode pedagogiske evner og er gode rollemodeller – de er Norges realfagshelter.
                            <br/><br/>Da studentene er tilstede i skoletiden skiller vi oss derfor fra andre tiltak ved at elevenes barriere for å delta er ikke-eksisterende. Dette gjør at vi når ut til alle –
                            ikke kun de som allerede er motiverte. <br/><br/>Vektorprogrammet sørger for at alle får hjelp i timen raskere og at undervisningen kan bli mer tilpasset de ulike elevgruppene.
                        </p>
                    </Grid.Column>
                    <Grid.Column width={6}>
                        <img src={'http://placehold.it/500x500'} style={{width:300,height:300}}/>
                    </Grid.Column>
                </Grid.Row>
                <Grid.Row>
                    <div className="links">
                        <a href={'#FAQ'}>FAQ</a>
                        <a href={'#kontakt-info'}>Kontakt-info</a>
                    </div>
                </Grid.Row>
                <Grid.Row>
                    <div id={"FAQ"} className="FAQ">
                        <h1>Ofte stilte spørsmål</h1><br/>
                        <h3>Er verv i Vektorprogrammet betalt?</h3>
                        <p>Nei, Vektorprogrammet er en frivillig, studentdrevet organisasjon, der ingen verv er betalt i form av norske kroner. Du får derimot delta på sosiale arrangementer og muligheten til å påvirke en organisasjon som er den største av sitt slag.
                        </p>
                        <h3>Hvor stor er arbeidsmengden for en vektorassistent?</h3>
                        <p>Enkel stilling tilsvarer 4 skoledager på en ungdomsskole, fordelt over 4 uker. Dobbel stilling tilsvarer 8 skoledager fordelt over åtte uker. En vanlig skoledag er som regel fra 08:00-14:00, og i tillegg kommer transporttid.
                        </p>
                        <h3>Kan jeg bestemme hvilken dag jeg vil være vektorassistent?</h3>
                        <p>Ja, på intervju blir du spurt om hvilke(n) dag(er) som passer for deg. Du vil ikke bli plassert på en dag du har sagt at du ikke kan.</p>
                        <h3>Får jeg en attest dersom jeg har vært vektorassistent?</h3>
                        <p>Ja, det gjør du. Disse deles normalt ut på avslutningen for vektorassistentene det tilhørende semesteret, men dersom du av en eller annen grunn ikke kunne delta, ta kontakt med ditt lokalstyre via mail. Vil du ha attest for tidligere semester? Ta også kontakt med ditt lokalstyre på mail.</p>
                        <h3>Hva gjør jeg dersom klassen jeg skal være i ikke er tilstede eller dersom det er feil i timeplanen jeg har fått fra Vektorprogrammet?</h3>
                        <p>Ta kontakt med din skolekoordinator og forklar situasjonen. Dette er den personen du fikk skoledokumentene av.</p>
                        <h3>Får jeg noen ekstra utgifter av å være vektorassistent?</h3>
                        <p>Nei, Vektorprogrammet dekker bussbilletter, t-skjorte og pedagogikk-kurs.</p>
                        <h3>Hva er pedagogikk-kurs?</h3>
                        <p>Alle nye vektorassistenter må delta på et pedagogikk-kurs i starten av semesteret for å få en enkel innføring i hvordan man kan lære bort til andre på en god måte.</p>
                        <h3>Jeg har vært vektorassistent før, må jeg på intervju eller pedagogikk-kurs igjen?</h3>
                        <p>Nei, alle tidligere vektorassistenter trenger kun å søke via nettsiden. For å få eventuelle bussbilletter må du møte opp på slutten av pedagogikk-kurset.</p>
                        <h3>Hvordan søker jeg team?</h3>
                        <p>Gå inn på denne siden her, finn et eller flere team du er interessert i. Hvis dette teamet tar opp nye medlemmer vil det være en knapp hvor du kan søke. Hvis det ikke er opptak kan du sende en mail til teamleder og si ifra at du er interessert.</p>
                        <h3>Hva er forskjellen på vektorassistent og teammedlem?</h3>
                        <p>Som vektorassistent vil man reise til ungdomsskolen som lærerassistent, mens som teammedlem er man med på å påvirke Vektorprogrammet som organisasjon. Som teammedlem blir man altså med i administrasjonen, og arbeidsoppgavene avhenger av hvilket team man er med i.</p>
                        <h3>Går det an å både være vektorassistent og med i team samtidig?</h3>
                        <p>Det er fullt mulig å være begge deler samtidig. Som vektorassistent vil man kun bli sendt ut 4 eller 8 ganger per semester. Dette gjør at arbeidsmengden er overkommelig, og kan fint kombineres med teamarbeid og studier.</p>
                        <h3>Jeg glemte søknadsfristen. Hva gjør jeg nå?</h3>
                        <p>Gå til 'Opptak & kontakt' i menyen og velg din region. Der finnes det ett kontaktskjema som du kan fylle ut, så finner vi ut av det sammen.</p>
                        <h3>I hvilke regioner holder Vektorprogrammet til?</h3>
                        <p>Trondheim, Oslo, Bergen, Ås og Tromsø</p>
                        <h3>Kan jeg være vektorassistent flere semestre?</h3>
                        <p>Ja, men du må huske å søke på nytt hvert semester! Du trenger ikke gå gjennom intervju og pedagogikk-kurs på nytt.</p>
                        <h3>Finner du ikke svar på det du lurer på?</h3>
                        <p>Gå til 'Opptak & kontakt' i menyen og velg din region. Der finnes det et kontaktskjema nederst som du kan fylle ut, så skal vi svare så raskt vi kan !</p>
                    </div>
                </Grid.Row>
                <Grid.Row>
                    <div id={"kontakt-info"}>
                        Kontakt-info Kontakt-info Kontakt-info Kontakt-info Kontakt-info Kontakt-info
                        Kontakt-info Kontakt-info Kontakt-info Kontakt-info Kontakt-info Kontakt-info
                        Kontakt-info Kontakt-info Kontakt-info Kontakt-info Kontakt-info Kontakt-info
                        Kontakt-info Kontakt-info Kontakt-info Kontakt-info Kontakt-info Kontakt-info
                        Kontakt-info Kontakt-info Kontakt-info Kontakt-info Kontakt-info Kontakt-info
                    </div>
                </Grid.Row>
            </Grid>
        )

    }
}

export default AboutUsPage;