import React, {Component} from 'react';
import { Image, Grid, Rail, Divider, List, Button} from 'semantic-ui-react';
import './AboutUsPage.css';
import ContactUsPopUp from '../components/ContactUsPopUp';
import logo from '../images/logo_condensed.png';

class AboutUsPage extends Component {
    constructor(props){
        super(props);

        this.state={
            contactUsVisible: false,
            contactUsRevealed: false
        };
        this.onContactUsPopUpClose = this.onContactUsPopUpClose.bind(this);
        this.handleContactUsNowClick = this.handleContactUsNowClick.bind(this);
    }

    onContactUsPopUpClose(){
        this.setState({
            contactUsVisible : false
        });
    }
    handleContactUsNowClick(){
        this.setState ({
                contactUsVisible: true,
                contactUsRevealed: true
            });
    }
    render() {
        return (
            <Grid centered columns={2}>

                <ContactUsPopUp show={this.state.contactUsVisible} onClose={this.onContactUsPopUpClose}/>

                <Grid.Column>
                        <Grid>
                            <Grid.Row>
                                <h1 className="aboutUs-header">Om Vektorprogrammet</h1>
                            </Grid.Row>
                            <Grid.Row columns={2}>
                                <Grid.Column>
                                    <div>
                                        <p className="aboutUs-text">
                                            Vektorprogrammet arbeider for å øke interessen for matematikk og realfag blant elever i grunnskolen. Vi er en nasjonal studentorganisasjon som sender studenter med utmerket
                                            realfagskompetanse til skoler for å hjelpe elevene i matematikktimene. Disse studentene har også gode pedagogiske evner og er gode rollemodeller – de er Norges realfagshelter.
                                            <br/><br/></p>
                                    </div>
                                </Grid.Column>
                                <Grid.Column>
                                    <Image className="aboutUs-image1" src={"http://via.placeholder.com/350x350/"}/>
                                </Grid.Column>
                            </Grid.Row>
                            <Grid.Row columns={2}>
                                <Grid.Column>
                                    <Image className="aboutUs-image2" src={"http://via.placeholder.com/350x350/"}/>
                                </Grid.Column>
                                <Grid.Column>
                                    <div>
                                        <p className="aboutUs-text2">
                                            Da studentene er tilstede i skoletiden skiller vi oss derfor fra andre tiltak ved at elevenes barriere for å delta er ikke-eksisterende. Dette gjør at vi når ut til alle –
                                            ikke kun de som allerede er motiverte. <br/><br/>Vektorprogrammet sørger for at alle får hjelp i timen raskere og at undervisningen kan bli mer tilpasset de ulike elevgruppene.
                                        </p>
                                    </div>
                                </Grid.Column>
                            </Grid.Row>
                            <Divider fitted/>
                            <Grid.Row>
                                <div id={"FAQ"} className="aboutUs-FAQ">
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
                            <Divider fitted/>
                            <Grid.Row>
                                <div id={"kontakt-info"} style={{ marginLeft: 'var(--main-margin)',
                                    marginRight: 'var(--main-margin)', marginTop: 20}}>
                                    <section>
                                        <ul style={{listStyle: 'none',margin: 0, display: 'inline-block', borderBottom: '1px solid #e0e0e0'}}>
                                            <li style={{fontSize: 20, fontWeight: 700}}>
                                                Kontakt:
                                                <li style={{display: 'inline-block', fontSize: '17', marginLeft: 25}}>
                                                    <a href="mailto:NTNU@gmail.com" style={{color: '#2196f3', textDecoration: 'none'}}>Trondheim - NTNU</a>
                                                </li>
                                                <li style={{display: 'inline-block', fontSize: '17', marginLeft: 25}}>
                                                    <a href="mailto:HIST@gmail.com" style={{color: '#2196f3', textDecoration: 'none'}}>Trondheim - HIST</a>
                                                </li>
                                                <li style={{display: 'inline-block', fontSize: '17', marginLeft: 25}}>
                                                    <a href="mailto:NMBU@gmail.com" style={{color: '#2196f3', textDecoration: 'none'}}>Ås</a>
                                                </li>
                                                <li style={{display: 'inline-block', fontSize: '17', marginLeft: 25}}>
                                                    <a href="mailto:UiO@gmail.com" style={{color: '#2196f3', textDecoration: 'none'}}>Oslo</a>
                                                </li>
                                            </li>
                                        </ul>
                                    </section>
                                </div>
                            </Grid.Row>
                        </Grid>
                        <Rail position='right'>
                                <List size={"massive"}>
                                    <List.Item icon='question circle' content={<a href={'#FAQ'}>FAQ</a>} />
                                    <List.Item icon='envelope' content={<a href={'#kontakt-info'}>Kontaktinformasjon</a>} />
                                    <List.Item
                                        icon='talk'
                                        content={
                                                <Button basic color='blue' onClick={this.handleContactUsNowClick}>Kontakt oss nå!</Button>
                                        }
                                    />
                                    </List>
                        </Rail>
                </Grid.Column>
            </Grid>
        )
    }
}

export default AboutUsPage;