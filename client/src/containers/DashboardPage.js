import React, {Component} from 'react';
import { Switch, Route } from 'react-router-dom';
import './DashboardPage.css';

import AboutUsPage from "./AboutUsPage";
import HomePage from './HomePage';
import Menu from '../components/Dashboard/Menu';

import {
  Responsive
} from 'semantic-ui-react';

class DashboardPage extends Component {
  render() {
    return (
        <div className="dashboard-page">
          <Responsive as={Menu} minWidth={Responsive.onlyTablet.minWidth} />
          <div className="dashboard-content">
            <Switch>
              <Route exact path='/dashboard' component={HomePage}/>
              <Route exact path='/dashboard/profil' component={AboutUsPage}/>
            </Switch>
          </div>
        </div>
    );
  }
}

export default DashboardPage;
