import React, { Component } from 'react';

import { connect } from 'react-redux';

import './HomePage.css';
import { Button, Image, Grid, Responsive } from 'semantic-ui-react';
import { Link } from 'react-router-dom';
import SponsorList from '../components/SponsorList';
import hero from '../images/blackboard.png';

class HomePage extends Component {

  render() {
    return (
      <Grid className="homepage" padded>
        <Grid.Row className="hero-section">
          <Grid.Column mobile={16} tablet={8} computer={8}>
            <Responsive maxWidth={Responsive.onlyMobile.maxWidth}>
              <h1>Vektorprogrammet</h1>
            </Responsive>
            <Image className="hero-image" src={hero} alt={'Vektorprogrammet'}/>
          </Grid.Column>
          <Grid.Column mobile={16} tablet={8} computer={8}>
            <div className="hero-content">
              <Responsive minWidth={Responsive.onlyMobile.maxWidth}>
                <h1>Vektorprogrammet</h1>
              </Responsive>
              <p>- sender studenter til ungdomsskoler for å hjelpe til som
                assistentlærere i matematikkundervisningen</p>
              <Link to={'/assistenter'}>
                <Button color={'green'} className="hero-cta">LES MER OG BLI ASSISTENT</Button>
              </Link>
            </div>
          </Grid.Column>
        </Grid.Row>

        <h2>Sponsorer</h2>
        <SponsorList sponsors={this.props.sponsors}/>
      </Grid>
    );
  }
}

const mapStateToProps = state => ({
  sponsors: state.sponsors,
});

export default connect(mapStateToProps)(HomePage);
