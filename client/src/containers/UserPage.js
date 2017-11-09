import React from 'react';
import { connect } from 'react-redux';

import PageHeader from '../components/PageHeader';
import ProfileFormContainer from './ProfileFormContainer';

import './UserPage.css';

const UserPage = ({user}) => (
  <div className="container user-page">
    <PageHeader>
      <h1 className="user-page-header">{user.first_name} {user.last_name}</h1>
    </PageHeader>
    <p className="user-page-subheader">Endre din profil</p>
    <ProfileFormContainer/>
  </div>
);

const mapStateToProps = state => ({
  user: state.user
});

export default connect(mapStateToProps)(UserPage);
