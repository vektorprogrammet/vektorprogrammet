import React from "react";
import {connect} from "react-redux";
import {Switch, Route} from "react-router-dom";
import "./DashboardPage.css";
import Dashboard from "../components/Dashboard/Dashboard";
import UserPage from "./UserPage";
import ReceiptPage from "./ReceiptPage";
import Menu from "../components/Dashboard/Menu";
import {Responsive} from "semantic-ui-react";

const DashboardPage = ({user}) => (

  <div className="dashboard-page">
      <Responsive as={Menu} user={user} minWidth={Responsive.onlyTablet.minWidth}/>
      <div className="dashboard-content">
          <Switch>
              <Route exact path='/dashboard' render={() => <Dashboard user={user}/>}/>
              <Route exact path='/dashboard/profil' render={() => <Dashboard user={user}/>}/>
              <Route exact path='/dashboard/profil/endre' render={() => <UserPage user={user}/>}/>
              <Route exact path='/dashboard/utlegg' render={() => <ReceiptPage/>}/>
          </Switch>
      </div>
  </div>
);

const mapStateToProps = state => ({
    user: state.user,
});

export default connect(mapStateToProps)(DashboardPage);
