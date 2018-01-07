import React, { Component } from 'react';

import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { reduxForm, formValueSelector, change } from 'redux-form';

import { postApplication } from '../actions/application';
import { getActiveDepartments } from '../selectors/department';
import ApplicationForm from '../components/ApplicationForm/ApplicationForm';
import ApplicationFormSubmitted from '../components/ApplicationForm/ApplicationFormSubmitted';


class ApplicationFormContainer extends Component {
  handleSubmit = this.props.handleSubmit(values => {
    this.props.postApplication(values)
  });

  render() {
    if (this.props.activeDepartments.indexOf(this.props.selectedDepartment) === -1) {
      // return <p>Newsletter</p>
    }

        if(this.props.application.hasOwnProperty("firstName")) {
          return (<ApplicationFormSubmitted/>);
        }
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
    application: state.application,
    departments: state.departments,
    activeDepartments: getActiveDepartments(state),
    initialValues: {
      department: getActiveDepartments(state)[0]
    },
  selectedDepartment: selector(state, 'department'),
});

const clearFieldOfStudy = () => change(form, 'fieldOfStudyId', '');

const mapDispatchToProps = dispatch => bindActionCreators({
  postApplication,
  clearFieldOfStudy
}, dispatch);

export default connect(mapStateToProps, mapDispatchToProps)(ApplicationFormContainer);
