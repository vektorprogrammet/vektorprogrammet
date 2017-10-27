import React, {Component} from 'react';
import './HomePage.css';
import {Button, Image, Grid} from 'semantic-ui-react';
import { Link } from 'react-router-dom';
import SponsorList from '../components/SponsorList';
import {SponsorApi} from '../api/SponsorApi';
import hero from '../images/blackboard.png';

class HomePage extends Component {
  constructor(props) {
    super(props);
    this.state = {
      sponsors: [],
      mainSponsor: {}
    }
  }

  async componentDidMount() {
    const sponsors = SponsorApi.getAll();
    const mainSponsor = SponsorApi.get(1);
    this.setState({
      sponsors: await sponsors,
      mainSponsor: await mainSponsor
    });
  }

  render() {
    return (
        <Grid className="homepage" padded>
          <Grid.Row className="hero-section">
            <Grid.Column mobile={16} tablet={8} computer={8}>
              <Image className="hero-image" src={hero} alt={'Vektorprogrammet'}/>
            </Grid.Column>
            <Grid.Column mobile={16} tablet={8} computer={8}>
              <div className="hero-content">
                <h1>Vektorprogrammet</h1>
                <p>- sender studenter til ungdomsskoler for å hjelpe til som
                  assistentlærere i matematikkundervisningen</p>
                <Link to={'/assistenter'}>
                  <Button color={'green'} className="hero-cta">LES MER OG BLI ASSISTENT</Button>
                </Link>
              </div>
            </Grid.Column>
          </Grid.Row>

          <h2>Hovedsponsor: {this.state.mainSponsor.name}</h2>
          <h3>Andre sponsorer</h3>
          <SponsorList sponsors={this.state.sponsors}/>
        </Grid>
    );
  }
}

export default HomePage;
