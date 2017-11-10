import React, { Component } from 'react';
import { Switch, Route } from 'react-router-dom';

import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { fetchDepartments } from '../actions/department';
import { fetchSponsors } from '../actions/sponsor';
import { Assistant } from '../authorization';

import Header from './Header';
import HomePage from './HomePage';
import AssistantPage from './AssistantPage';
import TeamPage from './TeamPage';
import AboutUsPage from './AboutUsPage';
import ContactPage from './ContactPage';
import LoginPage from './LoginPage';
import ReceiptPage from './ReceiptPage';
import DashboardPage from './DashboardPage';
import UserPage from './UserPage';
import Error404 from '../components/Error/Error404';

class App extends Component {
  componentDidMount() {
    this.props.fetchDepartments();
    this.props.fetchSponsors();
  }

  render() {
    return (
      <div>
        <Header/>
        <Switch>
          <Route exact path='/' component={HomePage}/>
          <Route exact path='/assistenter' component={AssistantPage}/>
          <Route exact path='/team' component={TeamPage}/>
          <Route exact path='/om-oss' component={AboutUsPage}/>
          <Route exact path='/kontakt' component={ContactPage}/>
          <Route exact path='/login' component={LoginPage}/>
          <Route exact path='/utlegg' component={ReceiptPage}/>
          <Route exact path='/bruker' component={Assistant(UserPage)}/>
          <Route path='/dashboard' component={Assistant(DashboardPage)}/>
          <Route path='/' component={Error404}/>
        </Switch>
      </div>
    );
  }
}

const mapDispatchToProps = dispatch => bindActionCreators({
  fetchDepartments,
  fetchSponsors,
}, dispatch);

export default connect(null, mapDispatchToProps, null, {pure: false})(App);
