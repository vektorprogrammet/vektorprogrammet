import React, { Component } from 'react';
import { Switch, Route } from 'react-router-dom';
import Header from './Header';
import HomePage from './HomePage';

class App extends Component {
  render() {
    return (
        <div>
          <Header/>
          <Switch>
            <Route exact path='/' component={HomePage}/>
          </Switch>
        </div>
    );
  }
}

export default App;
