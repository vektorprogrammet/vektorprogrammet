import React from 'react';
import { connect } from 'react-redux';
import LoginPage from './containers/LoginPage';
import Error403 from './components/Error/Error403';

export default (allowedRoles) => {
  return WrappedComponent => {
    const WithAuthorization = props => {
      const {user} = props;
      if (!user) {
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
