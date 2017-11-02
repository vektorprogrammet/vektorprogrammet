import React from 'react';
import {Image, Grid, Responsive} from 'semantic-ui-react';
import './MainTextAboutUs.css';


export const MainTextAboutUs = () => {
    return(
        <Grid stackable={true} className={'main-text-about-us'}>
            {/*Move to own css-file*/}
            <Grid.Row className={'main-text-about-us-section'}>
                <Grid.Column width={9}>
                    <h2 className="aboutUs-header">Motivere elever</h2>
                    <p className="aboutUs-text">
                      Vektorprogrammet ønsker å øke matematikkforståelsen blant elever i grunnskolen. Forståelse gir
                      mestringsfølelse som fører til videre motivasjon. Siden matematikk er grunnlaget for alle realfag
                      er målet at dette også skal føre til motivasjon og videre utforskning av realfagene.
                    </p>
                </Grid.Column>
                <Grid.Column width={6} floated={'right'}>
                    <Image className="aboutUs-image1" src={"http://via.placeholder.com/350x350/"}/>
                </Grid.Column>
            </Grid.Row>
            <Grid.Row className={'main-text-about-us-section'}>
                <Responsive as={Grid.Column} width={6} minWidth={Responsive.onlyTablet.minWidth}>
                      <Image className="aboutUs-image2" src={"http://via.placeholder.com/350x350/"}/>
                </Responsive>
                <Grid.Column width={9} floated={'right'}>
                    <h2 className="aboutUs-header">Motivere studenter</h2>
                    <p className="aboutUs-text">
                      Vi har som mål at alle studentene skal sitte igjen mer motivert for videre studier etter å ha vært
                      vektorassistent. Av erfaring vet vi at muligheten til å formidle egen kunnskap og se at deres
                      arbeid gir elevene mestringsfølelse er en sterk motivasjonsfaktor. Videre arrangerer vi både
                      sosiale og faglige arrangementer for å forsterke denne motivasjonen.
                    </p>
                </Grid.Column>
                <Responsive as={Grid.Column} width={6} {...Responsive.onlyMobile}>
                  <Image className="aboutUs-image2" src={"http://via.placeholder.com/350x350/"}/>
                </Responsive>
            </Grid.Row>
            <Grid.Row className={'main-text-about-us-section'}>
                <Grid.Column width={16}>
                     <h2 className="aboutUs-header">En forsmak til læreryrket</h2>
                    <p className="aboutUs-text">
                      Siden studentene er tilstede i undervisningen får de en introduksjon til læreryrket. Mange som
                      studerer realfag vurderer en fremtid som lærer, og får gjennom oss muligheten til å få reell
                      erfaring.
                    </p>
                </Grid.Column>
                <Grid.Column width={16}>
                     <Image className="aboutUs-image2" src={"http://via.placeholder.com/950x335/"}/>
                </Grid.Column>
            </Grid.Row>
        </Grid>
    );
};

export default MainTextAboutUs;
