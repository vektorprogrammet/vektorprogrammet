import React, { Component } from 'react';
import { Switch, Route } from 'react-router-dom';
import Header from './Header';
import HomePage from './HomePage';
import AssistantPage from './AssistantPage';
import TeamPage from './TeamPage';
import AboutUsPage from './AboutUsPage';
import ContactPage from './ContactPage';

class App extends Component {
  render() {
    return (
        <div>
          <Header/>
          <Switch>gi
            <Route exact path='/' component={HomePage}/>
            <Route exact path='/assistenter' component={AssistantPage}/>
            <Route exact path='/team' component={TeamPage}/>
            <Route exact path='/om-oss' component={AboutUsPage}/>
            <Route exact path='/kontakt' component={ContactPage}/>
          </Switch>
        </div>
    );
  }
}

export default App;
