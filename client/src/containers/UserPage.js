import React from 'react';
import { connect } from 'react-redux';

import PageHeader from '../components/PageHeader';
import ProfileFormContainer from './ProfileFormContainer';

import './UserPage.css';

const UserPage = ({user}) => (
  <div className="container user-page">
    <header className="user-page-header">
      <h1>{user.first_name} {user.last_name}</h1>
      <p className="user-page-subheader">Endre din profil</p>
    </header>
    <div className="user-page-profile-form">
      <ProfileFormContainer/>
    </div>
  </div>
);

const mapStateToProps = state => ({
  user: state.user
});

export default connect(mapStateToProps)(UserPage);
