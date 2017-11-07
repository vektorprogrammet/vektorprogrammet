import React, { Component } from 'react';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import './LoginPage.css';

import { requestLogin } from '../actions/authentication';

class LoginPage extends Component {
    constructor(props) {
        super(props);

        this.state = {
            username: '',
            password: '',
            error: '',
        };
    }

    handleUsernameChange = (event) => {
        this.setState({username: event.target.value});
    };

    handlePasswordChange = (event) => {
        this.setState({password: event.target.value});
    };

    handleSubmit = () => {
        this.props.requestLogin(this.state);
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

const mapStateToProps = state => ({});
const mapDispatchToProps = dispatch => bindActionCreators({
    requestLogin,
}, dispatch);

export default connect(mapStateToProps, mapDispatchToProps)(LoginPage);
