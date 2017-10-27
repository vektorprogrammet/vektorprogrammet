import React from 'react';
import {Image, Grid, Responsive} from 'semantic-ui-react';
import './MainTextAboutUs.css';


export const MainTextAboutUs = () => {
    return(
        <Grid stackable={true} className={'main-text-about-us'}>
          <Grid.Row className={'main-text-about-us-section'}>
              <Grid.Column>
                  <h1 className="aboutUs-header">Om Vektorprogrammet</h1>
                  <p className="aboutUs-text">
                    Vektorprogrammet arbeider for å øke interessen for matematikk og realfag blant elever i grunnskolen. Vi
                    er en nasjonal studentorganisasjon som sender studenter med utmerket realfagskompetanse til skoler for å
                    hjelpe elevene i matematikktimene. Disse studentene har også gode pedagogiske evner og er gode
                    rollemodeller – de er Norges realfagshelter.
                  </p>
              </Grid.Column>
          </Grid.Row>
            {/*Move to own css-file*/}
            <Grid.Row columns={3} className={'main-text-about-us-section'}>
                <Grid.Column width={9}>
                    <h2 className="aboutUs-header">Motivere elever</h2>
                    <p className="aboutUs-text">
                      Vektorprogrammet ønsker å øke matematikkforståelsen blant elever i grunnskolen. Forståelse gir
                      mestringsfølelse som fører til videre motivasjon. Siden matematikk er grunnlaget for alle realfag
                      er målet at dette også skal føre til motivasjon og videre utforskning av realfagene.
                    </p>
                </Grid.Column>
                <Grid.Column width={6} floated={'right'} style={{justifyContent: 'right'}}>
                    <Image className="aboutUs-image1" src={"http://via.placeholder.com/350x350/"}/>
                </Grid.Column>
            </Grid.Row>
            <Grid.Row columns={3} className={'main-text-about-us-section'}>
                <Responsive as={Grid.Column} width={6} minWidth={Responsive.onlyTablet.minWidth}>
                      <Image className="aboutUs-image2" src={"http://via.placeholder.com/350x350/"}/>
                </Responsive>
                <Grid.Column width={9} floated={'right'}>
                    <h2 className="aboutUs-header">Motivere studenter</h2>
                    <p className="aboutUs-text">
                      Da studentene er tilstede i skoletiden skiller vi oss derfor fra andre tiltak ved at elevenes
                      barriere for å delta er ikke-eksisterende. Dette gjør at vi når ut til alle –
                      ikke kun de som allerede er motiverte. Vektorprogrammet sørger for at alle får hjelp i
                      timen raskere og at undervisningen kan bli mer tilpasset de ulike elevgruppene.
                    </p>
                </Grid.Column>
                <Responsive as={Grid.Column} width={6} maxWidth={Responsive.onlyTablet.minWidth}>
                  <Image className="aboutUs-image2" src={"http://via.placeholder.com/350x350/"}/>
                </Responsive>
            </Grid.Row>
            <Grid.Row columns={1}>
                <Grid.Column width={16}>
                     <h2 className="aboutUs-header">En forsmak til læreryrket</h2>
                    <p className="aboutUs-text">
                      Siden studentene er tilstede i undervisningen får de en introduksjon til læreryrket. Mange som
                      studerer realfag vurderer en fremtid som lærer, og får gjennom oss muligheten til å få reell
                      erfaring.
                    </p>
                </Grid.Column>
            </Grid.Row>
            <Grid.Row columns={1} className={'main-text-about-us-section'}>
                <Grid.Column width={16}>
                     <Image className="aboutUs-image2" src={"http://via.placeholder.com/950x335/"}/>
                </Grid.Column>
            </Grid.Row>
        </Grid>
    );
};

export default MainTextAboutUs;
