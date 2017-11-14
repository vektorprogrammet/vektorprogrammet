import React, { Component } from 'react';

import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { reduxForm } from 'redux-form';
import { requestUserUpdate } from '../actions/user';

import ProfileForm from '../components/UserEdit/ProfileForm';

class ProfileFormContainer extends Component {
  handleSubmit = this.props.handleSubmit(values => {
    this.props.requestUserUpdate(values)
  });

  render() {
    return (
      <ProfileForm
        user={this.props.user}
        onSubmit={this.handleSubmit}
      />
    );
  }
}

const form = 'user';

ProfileFormContainer = reduxForm({
  form,
  enableReinitialize : true
})(ProfileFormContainer);

const mapStateToProps = state => ({
  initialValues: state.user,
});

const mapDispatchToProps = dispatch => bindActionCreators({
  requestUserUpdate,
}, dispatch);

export default connect(mapStateToProps, mapDispatchToProps)(ProfileFormContainer);
