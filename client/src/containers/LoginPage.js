import React, {Component} from 'react';
import './LoginPage.css';

import {Authentication} from '../api/Authentication';

class LoginPage extends Component {
  constructor(props) {
    super(props);

    this.state = {
      username: '',
      password: '',
      error: ''
    }
  }

  handleUsernameChange = (event) => {
    this.setState({username: event.target.value});
  };

  handlePasswordChange = (event) => {
    this.setState({password: event.target.value});
  };

  handleSubmit = async() => {
    const response = await Authentication.login(this.state.username, this.state.password);
    if (response.status === 401) {
      this.setState({error: 'Feil brukernavn eller passord'});
      return;
    }
    const json = await response.json();
    if (!json.user) {
      this.setState({error: 'Noe gikk galt. Prøv igjen.'});
      return;
    }

    this.props.onLogin(JSON.parse(json.user));

    console.log(json.user);
  };

  clearError = () => {
    this.setState({error: ''});
  };

  render() {
    const error = this.state.error ? (
        <div className="error">{this.state.error}</div>
    ) : null;
    return (
      <div>
        {error}
        <input type="text" placeholder="Brukernavn"
               value={this.state.username}
               onChange={this.handleUsernameChange}
               onFocus={this.clearError}
        />
        <input type="password"
               placeholder="Passord"
               value={this.state.password}
               onChange={this.handlePasswordChange}
               onFocus={this.clearError}
        />
        <button onClick={this.handleSubmit}>Slipp meg inn!</button>
        <a href="/resetpassord">Glemt passord?</a>
      </div>
    );
  }
}

export default LoginPage;
