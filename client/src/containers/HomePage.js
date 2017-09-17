import React, { Component } from 'react';
import logo from '../images/logo.svg';
import './HomePage.css';
import {SponsorList} from '../components/SponsorList';
import {SponsorApi} from '../api/SponsorApi';

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
        <div className="App">
          <div className="App-header">
            <img src={logo} className="App-logo" alt="logo" />
            <h2>Welcome to React</h2>
          </div>
          <p className="App-intro">
            To get started, edit <code>src/App.js</code> and save to reload.
          </p>
          <h2>Hovedsponsor: {this.state.mainSponsor.name}</h2>
          <h3>Andre sponsorer</h3>
          <SponsorList sponsors={this.state.sponsors} />
        </div>
    );
  }
}

export default HomePage;
