/*TODO: It is not possible to access the other pages from this page, find out why*/
import React, {Component} from 'react';
import { Image, Grid, Rail, Divider, List, Button} from 'semantic-ui-react';
import './AboutUsPage.css';
import ContactUsPopUp from '../components/ContactUsPopUp';
import Faq from '../components/Faq';

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
        const faqQuestionsAndAnswers = [
            {question: "Whats you name=", answer: "Simon"},
            {question: "How old are you?", answer: "24"},
            {question: "Where do you live?", answer: "Trondheim"}
        ];

        return (
            <Grid columns={3}>
                <ContactUsPopUp show={this.state.contactUsVisible} onClose={this.onContactUsPopUpClose}/>
                <Grid.Column width={2}>
                </Grid.Column>
                <Grid.Column width={10}>
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
                                            <br/><br/>
                                        </p>
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
                                    <Faq questionsAndAnswers={faqQuestionsAndAnswers}/>
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
                </Grid.Column>
                <Grid.Column width={4}>
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
                </Grid.Column>
            </Grid>
        )
    }
}

export default AboutUsPage;