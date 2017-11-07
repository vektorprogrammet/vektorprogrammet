import React, { Component } from 'react';
import { Switch, Route } from 'react-router-dom';
import Header from './Header';
import HomePage from './HomePage';
import AssistantPage from './AssistantPage';
import TeamPage from './TeamPage';
import AboutUsPage from './AboutUsPage';
import ContactPage from './ContactPage';
import LoginPage from './LoginPage';
import ReceiptPage from './ReceiptPage';
import DashboardPage from './DashboardPage';

class App extends Component {
    constructor(props) {
        super(props);
        this.state = {
            user: {}
        }
    }
    componentDidMount() {
        const user = JSON.parse(localStorage.getItem('user'));
        if (user) {
            this.setState({user});
        }
    }
    handleLogin = (user) => {
        this.setState({user});
        localStorage.setItem('user', JSON.stringify(user));
    };
  render() {
      const loginPageWithProps = (props) => {
          return (
              <LoginPage onLogin={this.handleLogin} {...props} />
          );
      };
      const dashboardPageWithUser = (props) => {
          return (
              <DashboardPage user={this.state.user} {...props} />
          );
      };
    return (
        <div>
          <Header user={this.state.user} />
          <Switch>
            <Route exact path='/' component={HomePage}/>
            <Route exact path='/assistenter' component={AssistantPage}/>
            <Route exact path='/team' component={TeamPage}/>
            <Route exact path='/om-oss' component={AboutUsPage}/>
            <Route exact path='/kontakt' component={ContactPage}/>
            <Route exact path='/login' component={loginPageWithProps}/>
            <Route exact path='/utlegg' component={ReceiptPage}/>
            <Route path='/dashboard' component={dashboardPageWithUser}/>
          </Switch>
        </div>
    );
  }
}

export default App;
