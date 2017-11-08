import React, { Component } from 'react';

import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { fetchDepartments } from '../actions/department';

import ApplicationForm from '../components/ApplicationForm/ApplicationForm';

class ApplicationFormContainer extends Component {
  componentDidMount() {
    if (this.props.departments.length === 0) {
      this.props.fetchDepartments();
    }
  }

  render() {
    return (
      <ApplicationForm
        departments={this.props.departments}
        onSubmit={(values) => {console.log("Submitted!", values)}}
      />
    );
  }
}

const mapStateToProps = state => ({
  departments: state.departments.filter(d => d.active_admission)
});

const mapDispatchToProps = dispatch => bindActionCreators({
  fetchDepartments,
}, dispatch);

export default connect(mapStateToProps, mapDispatchToProps)(ApplicationFormContainer);
