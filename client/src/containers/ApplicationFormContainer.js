import React, { Component } from 'react';

import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { reduxForm, formValueSelector, change } from 'redux-form';

import { fetchDepartments } from '../actions/department';
import { postApplication } from '../actions/application';
import ApplicationForm from '../components/ApplicationForm/ApplicationForm';

class ApplicationFormContainer extends Component {
  componentDidMount() {
    if (this.props.departments.length === 0) {
      this.props.fetchDepartments();
    }
  }

  handleSubmit = this.props.handleSubmit(values => {
    this.props.postApplication(values)
  });

  render() {
    return (
      <ApplicationForm
        departments={this.props.departments}
        onSubmit={this.handleSubmit}
        selectedDepartment={this.props.selectedDepartment}
        departmentChange={this.props.clearFieldOfStudy}
      />
    );
  }
}

const form = 'application';
const selector = formValueSelector(form);

ApplicationFormContainer = reduxForm({
  form,
  enableReinitialize : true
})(ApplicationFormContainer);

const mapStateToProps = state => ({
  departments: state.departments.filter(d => d.active_admission),
  initialValues: {
    department: state.departments.filter(d => d.active_admission)[0]
  },
  selectedDepartment: selector(state, 'department'),
});

const clearFieldOfStudy = () => change(form, 'fieldOfStudyId', '');

const mapDispatchToProps = dispatch => bindActionCreators({
  fetchDepartments,
  postApplication,
  clearFieldOfStudy
}, dispatch);

export default connect(mapStateToProps, mapDispatchToProps)(ApplicationFormContainer);
