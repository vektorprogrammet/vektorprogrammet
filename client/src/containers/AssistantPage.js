import React, {Component} from 'react';
import { Grid, Header } from 'semantic-ui-react';
import './AssistantPage.css';

class AssistantPage extends Component {


  render() {
    return (
        <Grid divided='vertically'>
            {/* HEADER */}
            <Grid.Row columns={1}>
                <Grid.Column width={12} className="header-text">
                    <Header size="huge">Assistenter</Header>
                    <p>Vektorassistent er et frivillig verv der du reiser til en ungdomsskole én dag i uka for å hjelpe til som
                    lærerassistent i matematikk. En stilling som vektorassistent varer i 4 eller 8 uker, og du kan selv
                    velge hvilken ukedag som passer best for deg. </p>
                </Grid.Column>
            </Grid.Row>

            {/* SMÅ BILDER */}
            <Grid.Row columns={3}>
                <Grid.Column width={4} className="assistant-pics">
                    <img className="displayed" src={'http://placehold.it/125x125'}/>
                    <Header size="small">Fint å ha på CVen</Header>
                    <p className="center">Erfaring som arbeidsgivere setter pris på. Alle assistenter får en attest.</p>
                </Grid.Column>
                <Grid.Column width={4} className="assistant-pics">
                    <img className="displayed" src={'http://placehold.it/125x125'}/>
                    <Header size="small">Sosiale arrangementer</Header>
                    <p className="center">Alle assistenter blir invitert til arrangementer som fester, populærforedrag, bowling, grilling i parken, gokart og paintball.</p>
                    </Grid.Column>
                <Grid.Column width={4} className="assistant-pics">
                    <img className="displayed" src={'http://placehold.it/125x125'}/>
                    <Header size="small">Vær et forbilde</Header>
                    <p className="center">Her kommer en liten tekst om at vektorassistenter er superhelter i matteundervisningen.</p>
                </Grid.Column>
            </Grid.Row>

            {/* FORKLAREDNE TEKST #1*/}
            <Grid.Row columns={1}>
                <Grid.Column width={12} className="content-text">
                    <Header size="big">Læringsassistent i matematikk</Header>
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

            {/* FORKLAREDNE TEKST #2 */}
            <Grid.Row columns={1}>
                <Grid.Column width={12} className="content-text">
                    <Header size="big">Arbeidsoppgaver</Header>
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

            {/* OPPTAKSPROSESS */}



        </Grid>

    )
  }
}

export default AssistantPage;
