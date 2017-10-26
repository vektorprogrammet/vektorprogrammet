import React from 'react';
import {Image, Grid, List, Button} from 'semantic-ui-react';
import './MainTextAboutUs.css';


export const MainTextAboutUs = () => {
    return(
        <Grid>
            <Grid.Row>
                {/*Move to own css-file*/}
                <Grid stackable={true} style={{paddingBottom: 50}}>
                    <Grid.Row columns={3}>
                        <Grid.Column width={9}>
                            <h1 className="aboutUs-header">Om Vektorprogrammet</h1>
                            <p className="aboutUs-text">
                                Vektorprogrammet arbeider for å øke interessen for matematikk og realfag blant elever i grunnskolen. Vi er en nasjonal studentorganisasjon som sender studenter med utmerket
                                realfagskompetanse til skoler for å hjelpe elevene i matematikktimene. Disse studentene har også gode pedagogiske evner og er gode rollemodeller – de er Norges realfagshelter.
                                <br/><br/>
                            </p>
                        </Grid.Column>
                        <Grid.Column width={1}></Grid.Column>
                        <Grid.Column width={6} style={{justifyContent: 'right'}}>
                            <Image className="aboutUs-image1" src={"http://via.placeholder.com/350x350/"}/>
                        </Grid.Column>
                    </Grid.Row>
                </Grid>
            </Grid.Row>
            <Grid.Row>
                <Grid stackable={true} style={{paddingBottom: 50}}>
                    <Grid.Row columns={3}>
                        <Grid.Column width={6}>
                            <Image className="aboutUs-image2" src={"http://via.placeholder.com/350x350/"}/>
                        </Grid.Column>
                        <Grid.Column width={1}></Grid.Column>
                        <Grid.Column width={9}>
                            <h1 className="aboutUs-header">Motivere studenter</h1>
                            <p className="aboutUs-text">
                                Da studentene er tilstede i skoletiden skiller vi oss derfor fra andre tiltak ved at elevenes barriere for å delta er ikke-eksisterende. Dette gjør at vi når ut til alle –
                                ikke kun de som allerede er motiverte. <br/><br/>Vektorprogrammet sørger for at alle får hjelp i timen raskere og at undervisningen kan bli mer tilpasset de ulike elevgruppene.
                            </p>
                        </Grid.Column>
                    </Grid.Row>
                </Grid>
            </Grid.Row>
            <Grid.Row>
                <Grid stackable={true}>
                    <Grid.Row columns={1}>
                        <Grid.Column width={16}>
                             <h1  className="aboutUs-header">En forsmak til læreryrket</h1>
                            <p className="aboutUs-text">
                                Siden studentene er tilstede i undervisningen får de en introduksjon til læreryrket. Mange som studerer realfag vurderer en fremtid som lærer, og får gjennom oss muligheten til å få reell erfaring.
                            </p>
                        </Grid.Column>
                    </Grid.Row>
                </Grid>
            </Grid.Row>
            <Grid.Row>
                <Grid stackable={true}style={{paddingBottom: 50}}>
                    <Grid.Row columns={1}>
                        <Grid.Column width={16}>
                             <Image className="aboutUs-image2" src={"http://via.placeholder.com/950x335/"}/>
                        </Grid.Column>
                    </Grid.Row>
                </Grid>
            </Grid.Row>
        </Grid>
    );
};

export default MainTextAboutUs;