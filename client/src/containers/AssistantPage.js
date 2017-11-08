import React, { Component } from 'react';

import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { fetchDepartments } from '../actions/department';

import { Grid, Header, List } from 'semantic-ui-react';
import './AssistantPage.css';
import NewsletterPopUp from '../components/NewsletterPopUp';
import ApplicationFormContainer from './ApplicationFormContainer';
import PageHeader from '../components/PageHeader';


class AssistantPage extends Component {

  constructor(props) {
    super(props);
    this.state = {
      newsletterVisible: false,
      newsletterRevealed: false,
      width: window.innerWidth,
    };
  }

  handleScroll = () => {
    if (!this.state.newsletterRevealed && window.pageYOffset >= document.getElementById('newsletterTrigger').offsetTop) {
      this.setState({newsletterVisible: true, newsletterRevealed: true});
    }
  };

  onNewsletterPopUpClose = () => {
    this.setState({newsletterVisible: false});
  };

  handleWindowSizeChange = () => {
    this.setState({width: window.innerWidth});
  };

  componentWillMount() {
    window.addEventListener('scroll', this.handleScroll);
    window.addEventListener('resize', this.handleWindowSizeChange);
  }

  componentDidMount() {
    this.props.fetchDepartments();
  }

  componentWillUnmount() {
    window.removeEventListener('scroll', this.handleScroll);
    window.removeEventListener('resize', this.handleWindowSizeChange);
  }

  render() {
    const {width} = this.state.width;
    const isMobile = width <= 500;

    return (
      <div className='assistant-page'>
        {isMobile ? '' : <NewsletterPopUp show={this.state.newsletterVisible} onClose={this.onNewsletterPopUpClose}/>}

        {/* HEADER */}
        <div className="container">
          <PageHeader>
            <h1>Assistenter</h1>
            <p>Vektorassistent er et frivillig verv der du reiser til en ungdomsskole én dag i uka for å hjelpe til som
              lærerassistent i matematikk. En stilling som vektorassistent varer i 4 eller 8 uker, og du kan selv
              velge hvilken ukedag som passer best for deg. </p>
          </PageHeader>
        </div>

        {/* TODO: Fikse dette på mobil. Fikse bredden på teksten under bildene*/}
        {/* SMÅ BILDER */}
        <div className="container pictures">
          <Grid columns={3} padded>
            <Grid.Column width={5} className="assistantPics one centered">
              <img className="displayed" src={'http://placehold.it/125x125'} alt={'Verv på CV'}/>
              <Header as='h3'>Fint å ha på CVen</Header>
              <p className="centered">Erfaring som arbeidsgivere setter pris på. Alle assistenter får en
                attest.</p>
            </Grid.Column>
            <Grid.Column width={5} className="assistantPics two centered">
              <img className="displayed" src={'http://placehold.it/125x125'}
                   alt={'Sosiale arrangementer'}/>
              <Header as='h3'>Sosiale arrangementer</Header>
              <p className="centered">Alle assistenter blir invitert til arrangementer som fester,
                populærforedrag, bowling, grilling i parken, gokart og paintball.</p>
            </Grid.Column>
            <Grid.Column width={5} className="assistantPics three centered">
              <img className="displayed" src={'http://placehold.it/125x125'} alt={'Forbilde'}/>
              <Header as='h3'>Vær et forbilde</Header>
              <p className="centered">Her kommer en liten tekst om at vektorassistenter er superhelter i
                matteundervisningen.</p>
            </Grid.Column>
          </Grid>
        </div>


        {/* FORKLAREDNE TEKST #1*/}
        <div className="container">
          <PageHeader>
            <h2 id='newsletterTrigger' className="centered">Læringsassistent i matematikk</h2>
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
          </PageHeader>
        </div>

        <Grid.Row columns={1}><br/><br/><br/></Grid.Row>

        {/* FORKLAREDNE TEKST #2 */}
        <div className="container">
          <PageHeader>
            <h2 className="centered">Arbeidsoppgaver</h2>
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
          </PageHeader>
        </div>

        <Grid.Row columns={1}><br/><br/><br/></Grid.Row>

        {/* OPPTAKSPROSESS */}
        <div className="container">
          <PageHeader>
            <Grid columns={2}>
              <Grid.Column width={12} className="assistantContentText centered">
                <h2>Hvordan blir jeg Vektorassistent?</h2>
              </Grid.Column>

              <Grid.Column width={8} className="assistantListText1">

                <Header as='h3'>Opptakskrav</Header>
                <List bulleted className="assistantPageListe">
                  <List.Item>Du studerer på høgskole/universitet</List.Item>
                  <List.Item>Du har hatt R1/P2 på videregående</List.Item>
                  <List.Item>Du har tid til å dra til en ungdomsskole <br/>èn dag i uka (kl. 8-14)</List.Item>
                </List>
              </Grid.Column>

              <Grid.Column width={8} className="assistantListText2">
                <Header as='h3'>Opptaksprosessen</Header>
                <List ordered className="assistantPageListe">
                  <List.Item>Vektorprogrammet tar opp nye assistenter i starten av hvert semester</List.Item>
                  <List.Item>Send inn søknad på <a href="">opptakssiden</a> til ditt universitet</List.Item>
                  <List.Item>Møt opp på intervju (obligatorisk)</List.Item>
                  <List.Item>Dra på et gratis pedagogikkurs arrangert av Vektorprogrammet</List.Item>
                  <List.Item>Få tildelt en ungdomsskole som du og din vektorpartner skal dra til</List.Item>
                </List>
              </Grid.Column>
            </Grid>
          </PageHeader>
        </div>

        <Grid.Row columns={1}><br/><br/><br/></Grid.Row>

        {/* SØKEFORM */}
        <div className="container">
          <Grid.Row columns={1}>
            <Grid.Column width={9} className="assistantApplicationForm centered">
              <div>
                <Header as='h3'>Send oss din søknad</Header>
                <ApplicationFormContainer />
              </div>
            </Grid.Column>
          </Grid.Row>
        </div>

      </div>

    );
  }
}

const mapStateToProps = state => ({
  departments: state.departments,
});

const mapDispatchToProps = dispatch => bindActionCreators({
  fetchDepartments,
}, dispatch);

export default connect(mapStateToProps, mapDispatchToProps)(AssistantPage);
