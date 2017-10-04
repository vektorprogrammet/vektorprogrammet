import React, {Component} from 'react';
import { Grid, Header, List } from 'semantic-ui-react';
import './AssistantPage.css';
import NewsletterPopUp from '../components/NewsletterPopUp';


class AssistantPage extends Component {

    constructor(props){
        super(props);
        this.state={
            newsletterVisible: false,
            newsletterRevealed: false
        };
        this.handleScroll = this.handleScroll.bind(this);
        this.onNewsletterPopUpClose = this.onNewsletterPopUpClose.bind(this);
    }

    handleScroll(){
        if (!this.state.newsletterRevealed && window.pageYOffset > 800) { // TODO: justere litt på denne
            this.setState({newsletterVisible: true, newsletterRevealed: true});
        }
    }

    onNewsletterPopUpClose(){
        this.setState({newsletterVisible : false});
    }

    componentWillMount() {
        window.addEventListener('scroll', this.handleScroll);
    }


  render() {
    return (
        <Grid>

            <NewsletterPopUp show={this.state.newsletterVisible} onClose={this.onNewsletterPopUpClose}/>

            <Grid.Row columns={1}><br/><br/></Grid.Row>

            {/* HEADER */}
            <Grid.Row columns={1}>
                <Grid.Column width={12} className="assistant-header-text">
                    <Header as="h1">Assistenter</Header>
                    <p>Vektorassistent er et frivillig verv der du reiser til en ungdomsskole én dag i uka for å hjelpe til som
                    lærerassistent i matematikk. En stilling som vektorassistent varer i 4 eller 8 uker, og du kan selv
                    velge hvilken ukedag som passer best for deg. </p>
                </Grid.Column>
            </Grid.Row>

            <Grid.Row columns={1}><br/><br/><br/></Grid.Row>

            {/* SMÅ BILDER */}
            <Grid.Row columns={3}>
                <Grid.Column width={4} className="assistant-pics">
                    <img className="displayed" src={'http://placehold.it/125x125'}/>
                    <Header as='h4'>Fint å ha på CVen</Header>
                    <p className="center">Erfaring som arbeidsgivere setter pris på. Alle assistenter får en attest.</p>
                </Grid.Column>
                <Grid.Column width={4} className="assistant-pics">
                    <img className="displayed" src={'http://placehold.it/125x125'}/>
                    <Header as='h4'>Sosiale arrangementer</Header>
                    <p className="center">Alle assistenter blir invitert til arrangementer som fester, populærforedrag, bowling, grilling i parken, gokart og paintball.</p>
                    </Grid.Column>
                <Grid.Column width={4} className="assistant-pics">
                    <img className="displayed" src={'http://placehold.it/125x125'}/>
                    <Header as='h4'>Vær et forbilde</Header>
                    <p className="center">Her kommer en liten tekst om at vektorassistenter er superhelter i matteundervisningen.</p>
                </Grid.Column>
            </Grid.Row>

            <Grid.Row columns={1}><br/><br/><br/></Grid.Row>

            {/* FORKLAREDNE TEKST #1*/}
            <Grid.Row columns={1}>
                <Grid.Column width={12} className="content-text">
                    <Header as='h2'>Læringsassistent i matematikk</Header>
                    <p>Vektorprogrammet er en studentorganisasjon som sender realfagssterke studenter til grunnskolen for å
                        hjelpe elevene med matematikk i skoletiden. Vi ser etter deg som lengter etter en mulighet til å lære
                        bort kunnskapene du sitter med og ønsker å ta del i et sterkt sosialt fellesskap. Etter å ha vært
                        vektorassistent kommer du til å sitte igjen med mange gode erfaringer og nye venner på tvert av trinn og
                        linje.
                    </p>
                    <p>I tillegg vil du få muligheten til å delta på mange sosiale arrangementer, alt fra fest og
                        grilling til spillkveld. Samtidig arrangerer vi populærforedrag som er til for å øke motivasjonen din
                        for videre studier. Vi har hatt besøk av blant annet Andreas Wahl, Jo Røislien, Knut Jørgen Røed
                        Ødegaard og James Grime.
                    </p>
                </Grid.Column>
            </Grid.Row>

            <Grid.Row columns={1}><br/><br/><br/></Grid.Row>

            {/* FORKLAREDNE TEKST #2 */}
            <Grid.Row columns={1}>
                <Grid.Column width={12} className="content-text">
                    <Header as='h2'>Arbeidsoppgaver</Header>
                    <p>
                        Her trenger vi en tekst som forklarer hva en assistent gjør ute på skolen (får en gruppe elever på
                        grupperom,
                        hjelpe til med oppgaveløsing i klasserom osv.) Husk å få med at man samarbeider med en lærer og at
                        man
                        alltid blir sendt ut sammen med en annen vektorassistent. Også noen ord om hvor mye barn og lærere
                        setter
                        pris på vektorassistenter (motivasjon, bedre hjelp til oppgaveløsing, de sterke elevene får
                        spennende og
                        utfordrende oppgaver osv.)</p>
                </Grid.Column>
            </Grid.Row>

            <Grid.Row columns={1}><br/><br/><br/></Grid.Row>

            {/* OPPTAKSPROSESS */}
            <Grid.Row columns={2}>
                <Grid.Column width={12} className="content-text">
                    <Header as='h2'>Hvordan blir jeg Vektorassistent?</Header>
                </Grid.Column>

                <Grid.Column width={5} className="list-text">
                    <Grid.Row columns={1}><br/><br/></Grid.Row>
                    <Header as='h3'>Opptakskrav</Header>
                    <List bulleted>
                        <List.Item>Du studerer på høgskole/universitet</List.Item>
                        <List.Item>Du har hatt R1/P2 på videregående</List.Item>
                        <List.Item>Du har tid til å dra til en ungdomsskole <br/>èn dag i uka (kl. 8-14)</List.Item>
                    </List>
                </Grid.Column>

                <Grid.Column width={5} className="list-text">
                    <Grid.Row columns={1}><br/><br/></Grid.Row>
                    <Header as='h3'>Opptaksprosessen</Header>
                    <List ordered>
                        <List.Item>Vektorprogrammet tar opp nye assistenter i starten av hvert semester</List.Item>
                        <List.Item>Send inn søknad på <a href="">opptakssiden</a> til ditt universitet</List.Item>
                        <List.Item>Møt opp på intervju (obligatorisk)</List.Item>
                        <List.Item>Dra på et gratis pedagogikkurs arrangert av Vektorprogrammet</List.Item>
                        <List.Item>Få tildelt en ungdomsskole som du og din vektorpartner skal dra til</List.Item>
                    </List>
                </Grid.Column>
            </Grid.Row>
        </Grid>

    )
  }
}

export default AssistantPage;