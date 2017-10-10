import React from 'react';
import {Image, Grid} from 'semantic-ui-react';
import './MainTextAboutUs.css';


export const MainTextAboutUs = () => {
    return(
        <Grid>
            <Grid.Row>
                <h1 className="aboutUs-header">Om Vektorprogrammet</h1>
            </Grid.Row>
            <Grid.Row>
                <Grid>
                    <Grid.Row columns={2}>
                        <Grid.Column width={8}>
                            <div>
                                <p className="aboutUs-text">
                                    Vektorprogrammet arbeider for å øke interessen for matematikk og realfag blant elever i grunnskolen. Vi er en nasjonal studentorganisasjon som sender studenter med utmerket
                                    realfagskompetanse til skoler for å hjelpe elevene i matematikktimene. Disse studentene har også gode pedagogiske evner og er gode rollemodeller – de er Norges realfagshelter.
                                    <br/><br/>
                                </p>
                            </div>
                        </Grid.Column>
                        <Grid.Column width={8}>
                            <Image className="aboutUs-image1" src={"http://via.placeholder.com/350x350/"}/>
                        </Grid.Column>
                    </Grid.Row>
                </Grid>
            </Grid.Row>
            <Grid.Row>
                <Grid>
                    <Grid.Row columns={2}>
                        <Grid.Column width={8}>
                            <Image className="aboutUs-image2" src={"http://via.placeholder.com/350x350/"}/>
                        </Grid.Column>
                        <Grid.Column width={8}>
                            <div>
                                <p className="aboutUs-text2">
                                    Da studentene er tilstede i skoletiden skiller vi oss derfor fra andre tiltak ved at elevenes barriere for å delta er ikke-eksisterende. Dette gjør at vi når ut til alle –
                                    ikke kun de som allerede er motiverte. <br/><br/>Vektorprogrammet sørger for at alle får hjelp i timen raskere og at undervisningen kan bli mer tilpasset de ulike elevgruppene.
                                </p>
                            </div>
                        </Grid.Column>
                    </Grid.Row>
                </Grid>
            </Grid.Row>
        </Grid>
    );
};

export default MainTextAboutUs;