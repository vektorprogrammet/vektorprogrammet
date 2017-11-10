import React from 'react';
import { connect } from 'react-redux';
import LoginPage from './containers/LoginPage';
import Error403 from './components/Error/Error403';

const authorize = allowedRoles => {
  return WrappedComponent => {
    const WithAuthorization = props => {
      const {user} = props;
      if (!user || !user.isLoggedIn) {
        return <LoginPage {...props}/>;
      }
      if (allowedRoles.includes(user.role)) {
        return <WrappedComponent {...props} />;
      } else {
        return <Error403/>;
      }
    };

    return connect(state => {
      return {user: state.user};
    })(WithAuthorization);
  };
};

export const Assistant = authorize(['assistant', 'team_member', 'team_leader', 'admin']);
export const TeamMember = authorize(['team_member', 'team_leader', 'admin']);
export const TeamLeader = authorize(['team_leader', 'admin']);
export const Admin = authorize(['admin']);
